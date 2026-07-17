<script setup lang="ts">

import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
</script>

<template>
<Accordion type="single" class="" collapsible>
        <AccordionItem value="terms" class="border-none">
            <AccordionTrigger class="max-w-fit cursor-pointer text-xl items-center">Условия аренды</AccordionTrigger>
            <AccordionContent>
                <h3 class="text-left! text-2xl font-bold">⏱ Срок аренды</h3>
                <p>
                    Минимальный срок — 1 сутки. Также возможна аренда на выходные или неделю. Для
                    постоянных клиентов доступны скидки и акции. <b>Возврат велосипеда до 20:00 текущего дня</b>.
                    Возможность вернуть велосипед вернуть к открытию студии на следующий день обсуждается отдельно.
                </p>

                <h3  class="text-left! text-2xl font-bold">💰 Стоимость</h3>
                <p>Цена зависит от типа велосипеда и срока аренды. В среднем:</p>
                <ul>
                    <li>Шоссейные — от 2500 ₽ в сутки</li>
                    <li>Гравийные — от 2500 ₽</li>
                </ul>
                <p>Актуальные цены указаны в карточках моделей.</p>

                <h3 class="text-left! text-2xl font-bold">📄 Документы</h3>
                <p>
                    Оформляем простой договор. Нужен паспорт или водительское удостоверение
                </p>

                <h3 class="text-left! text-2xl font-bold">🛡 Залог</h3>
                <p>Два варианта залога:</p>
                <ul class="list-disc!">
                    <li>- Паспорт или водительское удостоверение</li>
                    <li>- 10 000 рублей наличными</li>
                </ul>

                <h3 class="text-left! text-2xl font-bold">🚫 Отмена брони</h3>
                <p>
                    Не позже чем за 2 недели до даты бронирования.
                </p>
            </AccordionContent>
        </AccordionItem>
        <AccordionItem value="who">
            <AccordionTrigger class="max-w-fit cursor-pointer  text-xl items-center">
                Кому подойдёт аренда велосипеда
            </AccordionTrigger>
            <AccordionContent>
                <p>
                    Наш прокат подходит как опытным спортсменам, так и новичкам. Вот кому особенно
                    может быть полезен Coffeeriders:
                </p>

                <ul>
                    <li>
                        🧑‍🚴‍♂️ <strong>Тем, кто готовится к гонке</strong> — Ironman, Gran Fondo,
                        шоссейные и gravel старты
                    </li>
                    <li>
                        🏕 <strong>Тем, кто планирует поездку за город</strong> или по Ленобласти
                    </li>
                    <li>
                        🎒 <strong>Путешественникам</strong>, которые не хотят везти свой велосипед
                    </li>
                    <li>
                        🛠 <strong>Тем, чей велосипед в ремонте</strong>, но хочется не пропускать
                        катание
                    </li>
                    <li>
                        🎉 <strong>Гостям города</strong>, которым нужен быстрый и удобный транспорт
                    </li>
                </ul>
            </AccordionContent>
        </AccordionItem>
    </Accordion>
</template>
