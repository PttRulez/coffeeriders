<?php

namespace App\Http\Requests\Admin;

use App\Models\WorkshopRepairOrder;
use App\Models\WorkshopSparePart;
use App\Support\TelegramUsernameExtractor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class WorkshopRepairOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user ? ($user->isAdmin() || $user->isMechanic()) : false;
    }

    public function rules(): array
    {
        $orderRouteParam = $this->route('workshopRepairOrder')
            ?? $this->route('workshop_repair_order');
        $orderId = is_object($orderRouteParam) ? $orderRouteParam->id : $orderRouteParam;

        return [
            'bike_name' => ['required', 'string', 'max:150'],
            'comment' => ['nullable', 'string', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:40', 'required_without:client_telegram'],
            'client_telegram' => ['nullable', 'string', 'max:120', 'required_without:client_phone'],
            'mechanic_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_mechanic', true)),
            ],
            'status' => ['required', 'string', 'in:' . implode(',', WorkshopRepairOrder::AVAILABLE_STATUSES)],
            'services' => ['required', 'array', 'min:1'],
            'services.*.workshop_service_id' => ['required', 'integer', 'exists:workshop_services,id'],
            'services.*.price_rub' => ['required', 'integer', 'min:0'],
            'spare_parts' => ['sometimes', 'array'],
            'spare_parts.*.external' => ['sometimes', 'boolean'],
            'spare_parts.*.workshop_spare_part_id' => ['nullable', 'integer', 'exists:workshop_spare_parts,id'],
            'spare_parts.*.name' => ['nullable', 'string', 'max:150'],
            'spare_parts.*.quantity' => ['required', 'integer', 'min:1'],
            'spare_parts.*.purchase_price_rub' => ['nullable', 'integer', 'min:0'],
            'spare_parts.*.sale_price_rub' => ['nullable', 'integer', 'min:0'],
            'photos' => ['sometimes', 'array'],
            'photos.*' => ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:8192'],
            'remove_photo_ids' => ['sometimes', 'array'],
            'remove_photo_ids.*' => [
                'integer',
                Rule::exists('workshop_repair_order_photos', 'id')->where(function ($query) use ($orderId) {
                    if (!$orderId) {
                        return $query;
                    }

                    return $query->where('workshop_repair_order_id', $orderId);
                }),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $telegram = $this->input('client_telegram');

            if ($telegram) {
                $normalizedTelegram = TelegramUsernameExtractor::extract($telegram);

                if ($normalizedTelegram === null) {
                    $v->errors()->add(
                        'client_telegram',
                        'Некорректный формат Telegram username. Используйте: @nickname, nickname, t.me/nickname или https://t.me/nickname'
                    );

                    return;
                }

                $this->merge(['client_telegram' => $normalizedTelegram]);
            }

            $mechanicId = $this->input('mechanic_id');
            $status = (string) $this->input('status', WorkshopRepairOrder::STATUS_PENDING);
            $statusNeedsMechanic = in_array($status, [
                WorkshopRepairOrder::STATUS_IN_WORK,
                WorkshopRepairOrder::STATUS_FINISHED,
            ], true);

            if ($statusNeedsMechanic && !$mechanicId) {
                $v->errors()->add(
                    'mechanic_id',
                    'Нельзя поставить статус "В работе" или "Завершено" без назначенного механика.'
                );
            }

            $orderRouteParam = $this->route('workshopRepairOrder')
                ?? $this->route('workshop_repair_order');
            $orderId = is_object($orderRouteParam) ? (int) $orderRouteParam->id : (int) $orderRouteParam;

            $requestedParts = collect($this->input('spare_parts', []))
                ->filter(fn ($row) => !filter_var(data_get($row, 'external', false), FILTER_VALIDATE_BOOLEAN))
                ->groupBy('workshop_spare_part_id')
                ->map(fn ($rows) => (int) collect($rows)->sum('quantity'));

            $sparePartsInput = collect($this->input('spare_parts', []));

            foreach ($sparePartsInput as $index => $row) {
                $isExternal = filter_var(data_get($row, 'external', false), FILTER_VALIDATE_BOOLEAN);

                if ($isExternal) {
                    if (!is_string(data_get($row, 'name')) || trim((string) data_get($row, 'name')) === '') {
                        $v->errors()->add("spare_parts.{$index}.name", 'Укажите название запчасти.');
                    }

                    if (!is_numeric(data_get($row, 'purchase_price_rub'))) {
                        $v->errors()->add("spare_parts.{$index}.purchase_price_rub", 'Укажите цену покупки.');
                    }

                    if (!is_numeric(data_get($row, 'sale_price_rub'))) {
                        $v->errors()->add("spare_parts.{$index}.sale_price_rub", 'Укажите цену продажи.');
                    }

                    continue;
                }

                if (!is_numeric(data_get($row, 'workshop_spare_part_id'))) {
                    $v->errors()->add("spare_parts.{$index}.workshop_spare_part_id", 'Выберите запчасть из каталога.');
                }
            }

            if ($v->errors()->isNotEmpty() || $requestedParts->isEmpty()) {
                return;
            }

            $partIds = $requestedParts->keys()->map(fn ($id) => (int) $id)->all();

            $currentOrderParts = collect();
            if ($orderId > 0) {
                $currentOrder = WorkshopRepairOrder::query()->find($orderId);
                $currentOrderParts = $currentOrder
                    ? $currentOrder->sparePartItems()
                        ->where('external', false)
                        ->whereNotNull('workshop_spare_part_id')
                        ->get(['workshop_spare_part_id', 'quantity'])
                        ->groupBy('workshop_spare_part_id')
                        ->map(fn ($rows) => (int) collect($rows)->sum('quantity'))
                    : collect();
            }

            $spareParts = WorkshopSparePart::query()
                ->whereIn('id', $partIds)
                ->get(['id', 'name', 'quantity'])
                ->keyBy('id');

            foreach ($requestedParts as $partId => $neededQuantity) {
                $part = $spareParts->get((int) $partId);
                if (!$part) {
                    continue;
                }

                $alreadyReserved = (int) ($currentOrderParts->get((int) $partId) ?? 0);
                $available = (int) $part->quantity + $alreadyReserved;

                if ($neededQuantity > $available) {
                    $v->errors()->add(
                        'spare_parts',
                        sprintf(
                            'Недостаточно запчасти "%s": доступно %d, запрошено %d.',
                            $part->name,
                            $available,
                            $neededQuantity
                        )
                    );
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'bike_name' => 'название велосипеда',
            'comment' => 'комментарий',
            'client_phone' => 'телефон клиента',
            'client_telegram' => 'telegram клиента',
            'mechanic_id' => 'механик',
            'status' => 'статус',
            'services' => 'услуги',
            'services.*.workshop_service_id' => 'услуга',
            'services.*.price_rub' => 'цена услуги',
            'spare_parts' => 'запчасти',
            'spare_parts.*.external' => 'внешняя запчасть',
            'spare_parts.*.workshop_spare_part_id' => 'запчасть',
            'spare_parts.*.name' => 'название запчасти',
            'spare_parts.*.quantity' => 'количество запчасти',
            'spare_parts.*.purchase_price_rub' => 'цена покупки запчасти',
            'spare_parts.*.sale_price_rub' => 'цена продажи запчасти',
            'photos' => 'фотографии',
            'photos.*' => 'фото',
            'remove_photo_ids' => 'удаляемые фото',
        ];
    }
}
