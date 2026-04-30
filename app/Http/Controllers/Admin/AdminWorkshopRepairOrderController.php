<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkshopRepairOrderRequest;
use App\Models\User;
use App\Models\WorkshopRepairOrder;
use App\Models\WorkshopService;
use App\Models\WorkshopSparePart;
use App\Services\ImageService;
use App\Support\TelegramUsernameExtractor;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminWorkshopRepairOrderController extends Controller
{
    public function index(): Response
    {
        $items = WorkshopRepairOrder::query()
            ->with([
                'photos' => fn ($query) => $query->limit(1),
                'sparePartItems:id,workshop_repair_order_id,quantity,purchase_price_rub,sale_price_rub',
                'mechanic:id,name,role',
            ])
            ->withSum('services as total_price_rub', 'workshop_repair_order_services.price_rub')
            ->latest()
            ->get();

        $items->each(function (WorkshopRepairOrder $item): void {
            $worksTotal = (int) ($item->total_price_rub ?? 0);
            $sparePartsTotal = $item->sparePartItems->sum(
                fn ($part) => (int) $part->quantity * (int) $part->sale_price_rub
            );
            $item->total_price_rub = $worksTotal + $sparePartsTotal;
        });

        $itemsByStatus = [
            WorkshopRepairOrder::STATUS_PENDING => [],
            WorkshopRepairOrder::STATUS_IN_WORK => [],
            WorkshopRepairOrder::STATUS_FINISHED => [],
        ];

        foreach ($items as $item) {
            $status = in_array($item->status, WorkshopRepairOrder::AVAILABLE_STATUSES, true)
                ? $item->status
                : WorkshopRepairOrder::STATUS_PENDING;

            $itemsByStatus[$status][] = $item;
        }

        return Inertia::render('adminka/workshop/repair-orders/Index', [
            'itemsByStatus' => $itemsByStatus,
        ]);
    }

    public function report(Request $request): Response
    {
        $validatedFilters = $request->validate([
            'period_from' => ['nullable', 'date'],
            'period_to' => ['nullable', 'date', 'after_or_equal:period_from'],
        ]);

        $periodFrom = data_get($validatedFilters, 'period_from');
        $periodTo = data_get($validatedFilters, 'period_to');

        if (!$periodFrom) {
            $periodFrom = Carbon::now()->startOfMonth()->toDateString();
        }

        if (!$periodTo) {
            $periodTo = Carbon::now()->endOfMonth()->toDateString();
        }

        return Inertia::render('adminka/workshop/repair-orders/Report', [
            'filters' => [
                'period_from' => $periodFrom,
                'period_to' => $periodTo,
            ],
            'summary' => $this->summaryForPeriod($periodFrom, $periodTo),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('adminka/workshop/repair-orders/Form', [
            'item' => null,
            'serviceCatalog' => $this->serviceCatalog(),
            'sparePartCatalog' => $this->sparePartCatalog(),
            'mechanics' => $this->mechanics(),
        ]);
    }

    public function store(
        WorkshopRepairOrderRequest $request,
        ImageService $imageService,
    ): RedirectResponse {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $imageService) {
            $order = WorkshopRepairOrder::create([
                'bike_name' => $validated['bike_name'],
                'comment' => $validated['comment'] ?? null,
                'client_phone' => $validated['client_phone'] ?? null,
                'client_telegram' => $this->normalizeTelegramUsername($validated['client_telegram'] ?? null),
                'mechanic_id' => $validated['mechanic_id'] ?? null,
                'status' => WorkshopRepairOrder::STATUS_PENDING,
                'finished_at' => null,
            ]);

            $this->syncServices($order, $validated['services']);
            $this->syncSpareParts($order, $validated['spare_parts'] ?? []);
            $this->syncIncome($order);
            $this->storeNewPhotos($order, $request->file('photos', []), $imageService);
        });

        return redirect()
            ->route('adminka.workshop.repair-orders.index')
            ->with('success', 'Велосипед принят в ремонт');
    }

    public function edit(WorkshopRepairOrder $workshopRepairOrder): Response
    {
            $workshopRepairOrder->load([
                'services' => fn ($query) => $query
                    ->with('category:id,name')
                    ->orderBy('name'),
            'sparePartItems' => fn ($query) => $query
                ->with('sparePart.category:id,name')
                ->orderBy('id'),
            'photos',
        ]);

        $workshopRepairOrder->setAttribute(
            'services',
            $workshopRepairOrder->services->map(function ($service) {
                return [
                    'id' => (int) $service->id,
                    'name' => (string) $service->name,
                    'category' => $service->category
                        ? ['id' => (int) $service->category->id, 'name' => (string) $service->category->name]
                        : null,
                    'pivot' => [
                        'price_rub' => (int) $service->pivot->price_rub,
                    ],
                ];
            })->values()
        );

        $workshopRepairOrder->setAttribute(
            'spare_parts',
            $workshopRepairOrder->sparePartItems->map(function ($item) {
                $isExternal = (bool) $item->external;

                return [
                    'external' => $isExternal,
                    'workshop_spare_part_id' => $isExternal ? null : $item->workshop_spare_part_id,
                    'quantity' => (int) $item->quantity,
                    'name' => $isExternal
                        ? (string) $item->name
                        : (string) ($item->sparePart?->name ?? ''),
                    'category_name' => $item->sparePart?->category?->name,
                    'stock_quantity' => $item->sparePart?->quantity,
                    'purchase_price_rub' => (int) $item->purchase_price_rub,
                    'sale_price_rub' => (int) $item->sale_price_rub,
                ];
            })->values()
        );

        return Inertia::render('adminka/workshop/repair-orders/Form', [
            'item' => $workshopRepairOrder,
            'serviceCatalog' => $this->serviceCatalog(),
            'sparePartCatalog' => $this->sparePartCatalog(),
            'mechanics' => $this->mechanics(),
        ]);
    }

    public function update(
        WorkshopRepairOrderRequest $request,
        WorkshopRepairOrder $workshopRepairOrder,
        ImageService $imageService,
    ): RedirectResponse {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $workshopRepairOrder, $imageService) {
            $workshopRepairOrder->update([
                'bike_name' => $validated['bike_name'],
                'comment' => $validated['comment'] ?? null,
                'client_phone' => $validated['client_phone'] ?? null,
                'client_telegram' => $this->normalizeTelegramUsername($validated['client_telegram'] ?? null),
                'mechanic_id' => $validated['mechanic_id'] ?? null,
                'status' => $validated['status'],
                'finished_at' => $this->resolveFinishedAt(
                    $workshopRepairOrder,
                    (string) $validated['status']
                ),
            ]);

            $this->syncServices($workshopRepairOrder, $validated['services']);
            $this->syncSpareParts($workshopRepairOrder, $validated['spare_parts'] ?? []);
            $this->syncIncome($workshopRepairOrder);
            $this->removePhotos($workshopRepairOrder, $validated['remove_photo_ids'] ?? [], $imageService);
            $this->storeNewPhotos($workshopRepairOrder, $request->file('photos', []), $imageService);
        });

        return redirect()
            ->route('adminka.workshop.repair-orders.index')
            ->with('success', 'Заказ ремонта обновлён');
    }

    public function destroy(
        WorkshopRepairOrder $workshopRepairOrder,
        ImageService $imageService,
    ): RedirectResponse {
        DB::transaction(function () use ($workshopRepairOrder, $imageService) {
            $this->restoreSparePartsQuantity($workshopRepairOrder);

            foreach ($workshopRepairOrder->photos as $photo) {
                $imageService->delete($photo->photo_url);
            }

            $workshopRepairOrder->delete();
        });

        return redirect()
            ->route('adminka.workshop.repair-orders.index')
            ->with('success', 'Заказ ремонта удалён');
    }

    public function updateStatus(Request $request, WorkshopRepairOrder $workshopRepairOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', WorkshopRepairOrder::AVAILABLE_STATUSES)],
        ]);

        $nextStatus = (string) $validated['status'];
        if (
            in_array($nextStatus, [WorkshopRepairOrder::STATUS_IN_WORK, WorkshopRepairOrder::STATUS_FINISHED], true)
            && !$workshopRepairOrder->mechanic_id
        ) {
            return back()->with('error', 'Нельзя поставить статус "В работе" или "Завершено" без назначенного механика.');
        }

        $workshopRepairOrder->update([
            'status' => $nextStatus,
            'finished_at' => $this->resolveFinishedAt($workshopRepairOrder, $nextStatus),
        ]);
        $this->syncIncome($workshopRepairOrder);

        return back()->with('success', 'Статус заказа обновлён');
    }

    private function serviceCatalog()
    {
        return WorkshopService::query()
            ->with('category:id,name')
            ->leftJoin('workshop_categories', 'workshop_categories.id', '=', 'workshop_services.workshop_category_id')
            ->orderBy('workshop_categories.sort_order')
            ->orderBy('workshop_categories.name')
            ->orderBy('workshop_services.name')
            ->select('workshop_services.*')
            ->get();
    }

    private function sparePartCatalog()
    {
        return WorkshopSparePart::query()
            ->with('category:id,name')
            ->leftJoin(
                'workshop_spare_part_categories',
                'workshop_spare_part_categories.id',
                '=',
                'workshop_spare_parts.workshop_spare_part_category_id'
            )
            ->orderBy('workshop_spare_part_categories.name')
            ->orderBy('workshop_spare_parts.name')
            ->select('workshop_spare_parts.*')
            ->get();
    }

    private function mechanics()
    {
        return User::query()
            ->where('is_mechanic', true)
            ->orderBy('name')
            ->get(['id', 'name', 'role']);
    }

    private function finishedOrdersForPeriodQuery(?string $periodFrom, ?string $periodTo): Builder
    {
        $query = WorkshopRepairOrder::query()
            ->where('status', WorkshopRepairOrder::STATUS_FINISHED);

        if ($periodFrom) {
            $query->whereDate(DB::raw('COALESCE(finished_at, updated_at)'), '>=', $periodFrom);
        }

        if ($periodTo) {
            $query->whereDate(DB::raw('COALESCE(finished_at, updated_at)'), '<=', $periodTo);
        }

        return $query;
    }

    private function summaryForPeriod(?string $periodFrom, ?string $periodTo): array
    {
        $summaryBaseQuery = $this->finishedOrdersForPeriodQuery($periodFrom, $periodTo);

        $summaryRow = (clone $summaryBaseQuery)
            ->selectRaw(
                'COALESCE(SUM(workshop_works_income_rub), 0) as workshop_works_income_rub,
                COALESCE(SUM(workshop_spare_parts_income_rub), 0) as workshop_spare_parts_income_rub,
                COALESCE(SUM(workshop_income_rub), 0) as workshop_income_rub,
                COALESCE(SUM(mechanic_income_rub), 0) as mechanic_income_rub'
            )
            ->first();

        $mechanicsIncome = (clone $summaryBaseQuery)
            ->whereNotNull('mechanic_id')
            ->where('mechanic_income_rub', '>', 0)
            ->with('mechanic:id,name')
            ->get(['id', 'mechanic_id', 'mechanic_income_rub'])
            ->groupBy('mechanic_id')
            ->map(function ($rows) {
                $name = (string) ($rows->first()?->mechanic?->name ?? 'Без имени');

                return [
                    'mechanic_id' => (int) $rows->first()->mechanic_id,
                    'mechanic_name' => $name,
                    'income_rub' => (int) $rows->sum('mechanic_income_rub'),
                ];
            })
            ->sortByDesc('income_rub')
            ->values();

        return [
            'workshop_works_income_rub' => (int) data_get($summaryRow, 'workshop_works_income_rub', 0),
            'workshop_spare_parts_income_rub' => (int) data_get($summaryRow, 'workshop_spare_parts_income_rub', 0),
            'workshop_income_rub' => (int) data_get($summaryRow, 'workshop_income_rub', 0),
            'mechanic_income_rub' => (int) data_get($summaryRow, 'mechanic_income_rub', 0),
            'mechanics' => $mechanicsIncome,
        ];
    }

    private function resolveFinishedAt(WorkshopRepairOrder $order, string $status): ?string
    {
        if ($status !== WorkshopRepairOrder::STATUS_FINISHED) {
            return null;
        }

        if ($order->finished_at) {
            return $order->finished_at->toDateTimeString();
        }

        return now()->toDateTimeString();
    }

    private function syncIncome(WorkshopRepairOrder $order): void
    {
        $worksMechanicIncome = (int) DB::table('workshop_repair_order_services')
            ->where('workshop_repair_order_id', $order->id)
            ->sum('mechanic_income_rub');
        $worksWorkshopIncome = (int) DB::table('workshop_repair_order_services')
            ->where('workshop_repair_order_id', $order->id)
            ->sum('workshop_income_rub');
        $sparePartsProfit = (int) $order->sparePartItems()
            ->get(['quantity', 'purchase_price_rub', 'sale_price_rub'])
            ->sum(fn ($part) => ((int) $part->sale_price_rub - (int) $part->purchase_price_rub) * (int) $part->quantity);

        $workshopIncome = $worksWorkshopIncome + $sparePartsProfit;

        $order->update([
            'mechanic_income_rub' => $worksMechanicIncome,
            'workshop_works_income_rub' => $worksWorkshopIncome,
            'workshop_spare_parts_income_rub' => $sparePartsProfit,
            'workshop_income_rub' => $workshopIncome,
        ]);
    }

    private function syncServices(WorkshopRepairOrder $order, array $services): void
    {
        $order->services()->detach();

        foreach ($services as $service) {
            $price = (int) $service['price_rub'];
            $serviceId = (int) $service['workshop_service_id'];

            $mechanicIncome = intdiv($price, 2);
            $workshopIncome = $price - $mechanicIncome;

            $order->services()->attach($serviceId, [
                'price_rub' => $price,
                'mechanic_income_rub' => $mechanicIncome,
                'workshop_income_rub' => $workshopIncome,
            ]);
        }
    }

    private function syncSpareParts(WorkshopRepairOrder $order, array $spareParts): void
    {
        $stockSpareParts = collect($spareParts)
            ->filter(fn ($row) => !filter_var(data_get($row, 'external', false), FILTER_VALIDATE_BOOLEAN))
            ->values()
            ->all();

        $externalSpareParts = collect($spareParts)
            ->filter(fn ($row) => filter_var(data_get($row, 'external', false), FILTER_VALIDATE_BOOLEAN))
            ->values()
            ->all();

        $requestedByPart = collect($stockSpareParts)
            ->groupBy('workshop_spare_part_id')
            ->map(fn ($rows) => (int) collect($rows)->sum('quantity'))
            ->filter(fn (int $qty) => $qty > 0);

        $currentByPart = $order->sparePartItems()
            ->where('external', false)
            ->whereNotNull('workshop_spare_part_id')
            ->get(['workshop_spare_part_id', 'quantity'])
            ->groupBy('workshop_spare_part_id')
            ->map(fn ($rows) => (int) collect($rows)->sum('quantity'));

        $partIds = $requestedByPart->keys()
            ->merge($currentByPart->keys())
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $parts = collect();
        if ($partIds->isNotEmpty()) {
            $parts = WorkshopSparePart::query()
                ->whereIn('id', $partIds->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($partIds as $partId) {
                $part = $parts->get((int) $partId);
                if (!$part) {
                    continue;
                }

                $currentQty = (int) ($currentByPart->get((int) $partId) ?? 0);
                $requestedQty = (int) ($requestedByPart->get((int) $partId) ?? 0);
                $delta = $requestedQty - $currentQty;

                if ($delta > 0) {
                    if ((int) $part->quantity < $delta) {
                        throw ValidationException::withMessages([
                            'spare_parts' => sprintf(
                                'Недостаточно запчасти "%s": доступно %d, нужно добавить ещё %d.',
                                $part->name,
                                (int) $part->quantity,
                                $delta
                            ),
                        ]);
                    }

                    $part->decrement('quantity', $delta);
                    continue;
                }

                if ($delta < 0) {
                    $part->increment('quantity', abs($delta));
                }
            }
        }

        $order->sparePartItems()->where('external', false)->delete();

        $stockRows = $requestedByPart
            ->map(function ($qty, $partId) use ($parts): ?array {
                $part = $parts->get((int) $partId);
                if (!$part) {
                    return null;
                }

                return [
                    'external' => false,
                    'workshop_spare_part_id' => (int) $partId,
                    'name' => null,
                    'quantity' => (int) $qty,
                    'purchase_price_rub' => (int) $part->purchase_price_rub,
                    'sale_price_rub' => (int) $part->sale_price_rub,
                    'workshop_income_rub' => ((int) $part->sale_price_rub - (int) $part->purchase_price_rub) * (int) $qty,
                ];
            })
            ->filter()
            ->values()
            ->all();

        if (count($stockRows) > 0) {
            $order->sparePartItems()->createMany($stockRows);
        }

        $this->syncExternalSpareParts($order, $externalSpareParts);
    }

    private function syncExternalSpareParts(WorkshopRepairOrder $order, array $externalSpareParts): void
    {
        $order->sparePartItems()->where('external', true)->delete();

        $rows = collect($externalSpareParts)
            ->map(function (array $row): array {
                return [
                    'external' => true,
                    'workshop_spare_part_id' => null,
                    'name' => trim((string) ($row['name'] ?? '')),
                    'quantity' => (int) ($row['quantity'] ?? 1),
                    'purchase_price_rub' => (int) ($row['purchase_price_rub'] ?? 0),
                    'sale_price_rub' => (int) ($row['sale_price_rub'] ?? 0),
                    'workshop_income_rub' => (
                        ((int) ($row['sale_price_rub'] ?? 0)) - ((int) ($row['purchase_price_rub'] ?? 0))
                    ) * (int) ($row['quantity'] ?? 1),
                ];
            })
            ->filter(fn (array $row) => $row['name'] !== '' && $row['quantity'] > 0)
            ->values()
            ->all();

        if (count($rows) > 0) {
            $order->sparePartItems()->createMany($rows);
        }
    }

    private function restoreSparePartsQuantity(WorkshopRepairOrder $order): void
    {
        $currentByPart = $order->sparePartItems()
            ->where('external', false)
            ->whereNotNull('workshop_spare_part_id')
            ->get(['workshop_spare_part_id', 'quantity'])
            ->groupBy('workshop_spare_part_id')
            ->map(fn ($rows) => (int) collect($rows)->sum('quantity'))
            ->filter(fn (int $qty) => $qty > 0);

        if ($currentByPart->isEmpty()) {
            return;
        }

        $parts = WorkshopSparePart::query()
            ->whereIn('id', $currentByPart->keys()->all())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($currentByPart as $partId => $qty) {
            $part = $parts->get((int) $partId);
            if ($part) {
                $part->increment('quantity', $qty);
            }
        }
    }

    private function removePhotos(
        WorkshopRepairOrder $order,
        array $removePhotoIds,
        ImageService $imageService,
    ): void {
        if (count($removePhotoIds) === 0) {
            return;
        }

        $photos = $order->photos()->whereIn('id', $removePhotoIds)->get();
        foreach ($photos as $photo) {
            $imageService->delete($photo->photo_url);
            $photo->delete();
        }
    }

    private function storeNewPhotos(
        WorkshopRepairOrder $order,
        mixed $files,
        ImageService $imageService,
    ): void {
        if (!$files) {
            return;
        }

        $files = is_array($files) ? $files : [$files];
        $baseOrder = (int) ($order->photos()->max('sort_order') ?? -1) + 1;
        foreach ($files as $index => $file) {
            $url = $imageService->save($file, 'workshop-repair-orders');
            $order->photos()->create([
                'photo_url' => $url,
                'sort_order' => $baseOrder + $index,
            ]);
        }
    }

    private function normalizeTelegramUsername(?string $value): ?string
    {
        return TelegramUsernameExtractor::extract($value);
    }
}
