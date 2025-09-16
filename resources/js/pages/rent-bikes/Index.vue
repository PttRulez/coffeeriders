<script setup lang="ts">
import BikeCard from '@/components/BikeCard.vue';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/shadecn/accordion';
import { BikeCategory } from '@/types/enums';
import { Bike } from '@/types/rent-bikes';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

type Props = {
    bikes: Bike[];
};

const { bikes } = defineProps<Props>();

const groupedBikes = computed(() => {
    return bikes.reduce(
        (acc, cur) => {
            if (!acc[cur.category]) {
                acc[cur.category] = [];
            }
            acc[cur.category].push(cur);
            return acc;
        },
        {} as Record<BikeCategory, Bike[]>,
    );
});

const getCategoryName = (name: BikeCategory): string => {
    switch (name) {
        case BikeCategory.Gravel:
            return 'Гравийники';
        case BikeCategory.Road:
            return 'Шоссеры';
        case BikeCategory.MTB:
            return 'МТБ';
        default:
            return 'Другие';
    }
};
</script>

<template>
    <Head title="Аренда велосипедов в СПб – Шоссейные, Гравийные, МТБ">
        <meta
            name="description"
            content="В холодное время вело любители выбирают тренировки в зале вместо уличных поездок. В
            Санкт-Петербурге наша велостудия предлагает занятия на современных станках Tacx Neo 2T и
            Zwift байках."
        />
    </Head>

    <h1 class="px-5 text-center max-md:text-[18px]!">
        Прокат велосипедов в Санкт-Петербурге с прозрачным сервисом
    </h1>

    <Accordion type="single" class="" collapsible>
        <AccordionItem value="terms" class="border-none">
            <AccordionTrigger class="max-w-fit cursor-pointer text-xl items-center">Условия аренды</AccordionTrigger>
            <AccordionContent>
                <h3 class="text-left! text-2xl font-bold">⏱ Срок аренды</h3>
                <p>
                    Минимальный срок — 1 сутки. Также возможна аренда на выходные или неделю. Для
                    постоянных клиентов доступны скидки и акции.
                </p>

                <h3  class="text-left! text-2xl font-bold">Стоимость</h3>
                <p>Цена зависит от типа велосипеда и срока аренды. В среднем:</p>
                <ul>
                    <li>Шоссейные — от 2500 ₽ в сутки</li>
                    <li>Гравийные — от 2700 ₽</li>
                    <li>MTB — от 2000 ₽</li>
                </ul>
                <p>Актуальные цены указаны в карточках моделей.</p>

                <h3 class="text-left! text-2xl font-bold">📄 Документы</h3>
                <p>
                    Оформляем простой договор. Нужен только паспорт и возвратный залог в 10 000
                    рублей.
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

    <template v-for="(bikes, categoryName) in groupedBikes" :key="categoryName">
        <h2 class="text-center text-2xl">
            <Link :href="route('rent-bikes.category', { categoryName })">
                {{ getCategoryName(categoryName) }}
            </Link>
        </h2>

        <div class="mb-10 grid grid-cols-1 gap-4 md:mb-20 md:grid-cols-3">
            <Link
                v-for="bike in bikes.sort((a, b) => b.prices[0].price - a.prices[0].price)"
                :key="bike.id"
                :href="route('rent-bikes.show', bike.id)"
            >
                <BikeCard :bike="bike" />
            </Link>
        </div>
    </template>

    <section class="space-y-5 break-words!">
        <p>
            Coffeeriders предлагает
            <strong>аренду велосипедов в Санкт-Петербурге с договором</strong> — прозрачно,
            оперативно и по-человечески. Мы быстро отвечаем, не усложняем процесс и всегда на связи.
            Все модели проходят регулярное техническое обслуживание и укомплектованы всем
            необходимым.
        </p>

        <div>
            <p>В прокате доступны:</p>
            <ul>
                <li>
                    <strong>
                        <Link
                            :href="
                                route('rent-bikes.category', { categoryName: BikeCategory.Road })
                            "
                            class="text-blue-400"
                        >
                            Шоссейные велосипеды
                        </Link>
                    </strong>
                    — лёгкие, быстрые, идеально подходят для асфальта, стартов и тренировок;
                </li>
                <li>
                    <strong>
                        <Link
                            :href="
                                route('rent-bikes.category', { categoryName: BikeCategory.Gravel })
                            "
                            class="text-blue-400"
                        >
                            Гравийные велосипеды
                        </Link>
                    </strong>
                    — универсальные и комфортные для поездок по смешанным покрытиям, вело прогулок и
                    путешествий;
                </li>
                <li>
                    <strong>
                        <Link
                            :href="route('rent-bikes.category', { categoryName: BikeCategory.MTB })"
                            class="text-blue-400"
                        >
                            Горные (MTB) велосипеды
                        </Link>
                    </strong>
                    — надёжные и амортизированные, подойдут для лесных троп и пересечённой
                    местности.
                </li>
            </ul>
        </div>
        <p>
            Все велосипеды отобраны действующими спортсменами — мастерами спорта и основателями
            нашей вело команды. Мы доверяем им сами и уверенно предлагаем вам.
        </p>
        <p>
            Самовывоз —
            <Link :href="route('contacts')" class="text-blue-400">Ярославский пр. 32</Link>
            . Удобно заехать на машине или добраться на метро. Вы можете
            <strong>арендовать велосипед на день, выходные или неделю</strong>. Также доступны
            шлемы, фляги и крепления.
        </p>
        <p>
            Если вы готовитесь к гонке, планируете поездку за город или просто хотите провести
            активные выходные — Coffeeriders обеспечит вам качественный велосипед и понятный сервис
        </p>

        <h2>Как происходит аренда</h2>
        <p>
            Мы сделали аренду велосипедов простой и понятной. Ниже — пошаговая инструкция, как взять
            шоссейный, гравийный или горный велосипед в прокат.
        </p>

        <ol>
            <li>
                <strong>1. Выберите велосипед.</strong> Ознакомьтесь с доступными моделями на этой
                странице. Мы сдаём в аренду только проверенные велосипеды: шоссейные, гравийные и
                MTB — в отличном состоянии, с понятной комплектацией.
            </li>

            <li>
                <strong>2. Свяжитесь с нами.</strong>Напишите нам в Telegram или позвоните по
                указанному номеру. Мы быстро отвечаем, подскажем подходящий размер и уточним детали.
            </li>

            <li>
                <strong>3. Приезжайте на самовывоз.</strong> Самовывоз осуществляется по адресу
                <Link :href="route('contacts')" class="text-blue-400">Ярославский пр. 32</Link>
                , недалеко от центра Санкт-Петербурга. Мы оформим аренду через простой договор и
                передадим вам полностью готовый велосипед.
            </li>

            <li>
                <strong>4. Возвращаете — и всё.</strong> После окончания срока аренды вы возвращаете
                велосипед в оговорённое время. Никаких сложностей — всё по договорённости и честно.
            </li>
        </ol>

        <div>
            <p>
                Все велосипеды обслужены, смазаны, с исправными тормозами и трансмиссией. Мы сдаём в
                прокат только технику, которой доверяем сами.
            </p>
            <p>
                Мы заботимся о том, чтобы ваша поездка была безопасной и комфортной. Вместе с
                велосипедом вы можете получить.
            </p>
        </div>

        <p>
            Также вы можете <strong>взять в аренду щлем</strong> — в нескольких размерах, бесплатно
            по запросу
        </p>
    </section>
</template>

<style scoped>
@media (max-width: 767px) {
    [data-slot='accordion-item'] h3 {
        justify-content: center;
    }
}
</style>
