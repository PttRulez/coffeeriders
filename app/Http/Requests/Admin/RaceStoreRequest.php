<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Rules\YandexMapUrlRule;
use App\Support\YandexMapUrlNormalizer;

class RaceStoreRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'description' => $this->normalizeDescription($this->input('description')),
            'yandex_map_url' => YandexMapUrlNormalizer::normalize($this->input('yandex_map_url')),
            'rank' => (int) $this->input('rank', 2),
        ]);
    }

    private function normalizeDescription(mixed $description): ?string
    {
        if (!is_string($description)) {
            return null;
        }

        $trimmedDescription = trim($description);
        if ($trimmedDescription === '') {
            return null;
        }

        $hasMediaTag = preg_match('/<(img|iframe|video|audio|svg|object|embed)\b/i', $trimmedDescription) === 1;
        if ($hasMediaTag) {
            return $trimmedDescription;
        }

        $plainText = html_entity_decode(strip_tags($trimmedDescription), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plainText = str_replace("\u{00A0}", ' ', $plainText);

        return trim($plainText) === '' ? null : $trimmedDescription;
    }

    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'race_types' => 'required|array|min:1',
            'race_types.*' => 'in:gravel,road,mtb,indoor,track,cyclocross',
            'rank' => 'required|integer|in:1,2,3',
            'in_our_studio' => 'required|boolean',
            'organizer_name' => 'nullable|string|max:255',
            'organizer_website_url' => 'nullable|url|max:2048',
            'registration_url' => 'nullable|url|max:2048',
            'yandex_map_url' => [
                'nullable',
                'url',
                'max:2048',
                new YandexMapUrlRule(),
            ],
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:8192',
            'price' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
            'clusters' => 'nullable|array',
            'clusters.*.id' => 'nullable|integer',
            'clusters.*.name' => 'nullable|string|max:255',
            'clusters.*.start_time' => 'nullable|date_format:H:i',
            'clusters.*.duration_minutes' => 'nullable|integer|min:1',
            'clusters.*.price' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите название гонки.',
            'name.max' => 'Название гонки не должно быть длиннее 255 символов.',
            'location.string' => 'Место проведения должно быть строкой.',
            'location.max' => 'Место проведения не должно быть длиннее 255 символов.',
            'description.string' => 'Описание должно быть строкой.',
            'date.required' => 'Укажите дату гонки.',
            'date.date' => 'Некорректный формат даты гонки.',
            'race_types.required' => 'Укажите хотя бы один тип гонки.',
            'race_types.array' => 'Типы гонки должны быть списком.',
            'race_types.min' => 'Укажите хотя бы один тип гонки.',
            'race_types.*.in' => 'Допустимые типы гонки: грэвел, шоссе, МТБ, indoor, трек, циклокросс.',
            'rank.required' => 'Укажите ранг гонки.',
            'rank.integer' => 'Ранг гонки должен быть числом.',
            'rank.in' => 'Ранг гонки должен быть 1, 2 или 3.',
            'in_our_studio.required' => 'Укажите, проводится ли гонка в нашей студии.',
            'in_our_studio.boolean' => 'Поле "в нашей студии" должно быть типа да/нет.',
            'organizer_name.string' => 'Имя организатора должно быть строкой.',
            'organizer_name.max' => 'Имя организатора не должно быть длиннее 255 символов.',
            'organizer_website_url.url' => 'Укажите корректную ссылку на сайт организатора.',
            'organizer_website_url.max' => 'Ссылка на сайт организатора не должна быть длиннее 2048 символов.',
            'registration_url.url' => 'Укажите корректную ссылку на регистрацию.',
            'registration_url.max' => 'Ссылка на регистрацию не должна быть длиннее 2048 символов.',
            'yandex_map_url.url' => 'Укажите корректную ссылку на Яндекс Карту.',
            'yandex_map_url.max' => 'Ссылка на Яндекс Карту не должна быть длиннее 2048 символов.',
            'cover_image.image' => 'Файл обложки должен быть изображением.',
            'cover_image.mimes' => 'Допустимые форматы обложки: jpeg, jpg, png, gif, webp.',
            'cover_image.max' => 'Размер обложки не должен превышать 8 МБ.',
            'price.integer' => 'Стоимость должна быть целым числом.',
            'price.min' => 'Стоимость не может быть отрицательной.',
            'is_published.boolean' => 'Поле публикации должно быть типа да/нет.',
            'clusters.array' => 'Кластеры должны быть переданы списком.',
            'clusters.*.id.integer' => 'Некорректный id кластера.',
            'clusters.*.name.string' => 'Название кластера должно быть строкой.',
            'clusters.*.name.max' => 'Название кластера не должно быть длиннее 255 символов.',
            'clusters.*.start_time.date_format' => 'Время старта кластера должно быть в формате ЧЧ:ММ.',
            'clusters.*.duration_minutes.integer' => 'Продолжительность кластера должна быть целым числом.',
            'clusters.*.duration_minutes.min' => 'Продолжительность кластера должна быть больше 0.',
            'clusters.*.price.integer' => 'Цена кластера должна быть целым числом.',
            'clusters.*.price.min' => 'Цена кластера не может быть отрицательной.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $inOurStudio = filter_var($this->input('in_our_studio'), FILTER_VALIDATE_BOOL);

            if (!$inOurStudio) {
                return;
            }

            $raceTypes = collect($this->input('race_types', []))
                ->filter()
                ->values()
                ->all();

            if (!in_array('indoor', $raceTypes, true)) {
                $validator->errors()->add('race_types', 'Для гонки в нашей студии добавьте тип indoor.');
            }

            if ((int) $this->input('price', -1) < 0) {
                $validator->errors()->add('price', 'Для гонки в нашей студии укажите стоимость.');
            }

            $clusters = $this->input('clusters', []);

            if (!is_array($clusters) || count($clusters) === 0) {
                $validator->errors()->add('clusters', 'Для indoor гонки нужен хотя бы один кластер.');
                return;
            }

            foreach ($clusters as $index => $cluster) {
                if (empty($cluster['name'])) {
                    $validator->errors()->add("clusters.$index.name", 'Укажите название кластера.');
                }
                if (empty($cluster['start_time'])) {
                    $validator->errors()->add("clusters.$index.start_time", 'Укажите время старта.');
                }
                if (empty($cluster['duration_minutes'])) {
                    $validator->errors()->add("clusters.$index.duration_minutes", 'Укажите длительность.');
                }
            }
        });
    }
}
