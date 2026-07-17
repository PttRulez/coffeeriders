<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';

type Photo = {
    alt: string;
    className?: string;
    src: string;
};

const { telegramLink } = usePage().props as { telegramLink: string };

const photos: Photo[] = [
    {
        alt: 'Байкфит в Coffeeriders - 1',
        className: 'md:col-span-2 md:row-span-2',
        src: '/img/pages/bikefit/jpeg/1.jpeg',
    },
    {
        alt: 'Байкфит в Coffeeriders - 2',
        src: '/img/pages/bikefit/jpeg/2.jpeg',
    },
    {
        alt: 'Байкфит в Coffeeriders - 3',
        src: '/img/pages/bikefit/jpeg/3.jpeg',
    },
    {
        alt: 'Байкфит в Coffeeriders - 4',

        src: '/img/pages/bikefit/jpeg/4.jpeg',
    },
    {
        alt: 'Байкфит в Coffeeriders - 5',
        src: '/img/pages/bikefit/jpeg/5.jpeg',
    },
    {
        alt: 'Байкфит в Coffeeriders - 9',
        className: 'md:col-span-2 md:row-span-2',
        src: '/img/pages/bikefit/jpeg/9.jpeg',
    },
    {
        alt: 'Байкфит в Coffeeriders - 7',
        src: '/img/pages/bikefit/jpeg/7.jpeg',
    },
    {
        alt: 'Байкфит в Coffeeriders - 8',
        src: '/img/pages/bikefit/jpeg/8.jpeg',
    },
    {
        alt: 'Байкфит в Coffeeriders - 6',
        src: '/img/pages/bikefit/jpeg/6.jpeg',
    },
    {
        alt: 'Байкфит в Coffeeriders - 10',

        src: '/img/pages/bikefit/jpeg/10.jpeg',
    },
];
</script>

<template>
    <Head title="Байкфит Coffeeriders">
        <meta
            name="description"
            content="Байкфит Retul в Санкт-Петербурге в студии CoffeeRiders: настройка посадки, подбор велосипеда на оборудовании Mueve, работа с комфортом, эффективностью и безопасностью."
        />
    </Head>

    <article class="space-y-10 md:space-y-14">
        <section
            class="relative -mx-5 min-h-[420px] overflow-hidden px-5 py-16 md:-mx-8 md:min-h-[520px] md:px-8 md:py-24"
        >
            <img
                src="/img/pages/bikefit/jpeg/5.jpeg"
                alt=""
                class="absolute inset-0 h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-black/45"></div>

            <div
                class="relative z-10 mx-auto flex h-full max-w-[1120px] flex-col justify-end gap-5 text-white"
            >
                <!--                <p class="text-sm font-semibold tracking-[0.14em] uppercase">Coffeeriders</p>-->
                <h1 class="max-w-[760px] text-left text-[40px] leading-[1.05] md:text-[72px]">
                    Байкфит Retul в СПб
                </h1>
                <p class="max-w-[620px] text-lg leading-7 text-white/90 md:text-xl">
                    Настройка посадки в уютной студии CoffeeRiders для комфорта, безопасности и
                    эффективности на велосипеде.
                </p>
            </div>
        </section>

        <section class="mx-auto grid max-w-[1120px] gap-7 md:grid-cols-[0.78fr_1fr] md:gap-10">
            <div class="space-y-4">
                <h2 class="text-left text-3xl md:text-4xl">Стоимость и формат</h2>
                <p class="text-base leading-7 text-muted-foreground">
                    Байкфит Retul проходит в студии CoffeeRiders. Продолжительность встречи зависит
                    от задачи: обычно от 2 до 4 часов.
                </p>
            </div>

            <div class="grid gap-3 text-sm md:grid-cols-2">
                <div class="border-l-2 border-foreground py-1 pl-4">
                    <p class="font-semibold">Байкфит</p>
                    <p class="mt-1 text-muted-foreground">12 000 ₽, от 2 до 4 часов.</p>
                </div>
                <div class="border-l-2 border-foreground py-1 pl-4">
                    <p class="font-semibold">Подбор велосипеда на Retul Müve</p>
                    <p class="mt-1 text-muted-foreground">10 000 ₽, от 2 часов.</p>
                </div>
                <div class="border-l-2 border-foreground py-1 pl-4">
                    <p class="font-semibold">Методика</p>
                    <p class="mt-1 text-muted-foreground">
                        Настройка посадки на обрудовании Retul.
                    </p>
                </div>
                <div class="border-l-2 border-foreground py-1 pl-4">
                    <p class="font-semibold">Перед фитом</p>
                    <p class="mt-1 text-muted-foreground">
                        Желательно отдохнуть 2 дня перед встречей.
                    </p>
                </div>
            </div>
        </section>

        <section class="mx-auto grid max-w-[1120px] gap-8 border-t pt-8 md:grid-cols-[0.9fr_1.1fr]">
            <div class="space-y-5">
                <h2 class="text-left text-3xl md:text-4xl">Фит-техник</h2>
                <figure class="overflow-hidden rounded-lg bg-muted">
                    <img
                        src="/img/pages/bikefit/jpeg/8.jpeg"
                        alt="Фит-техник Артем во время байкфита в CoffeeRiders"
                        class="aspect-[4/3] h-full w-full object-cover object-center"
                        loading="lazy"
                    />
                </figure>
            </div>
            <div class="space-y-4 leading-7 text-muted-foreground">
                <p>
                    Фит-техник Артем работает механиком и фит-техником в CoffeeRiders. Его путь в
                    велоиндустрии начался в 2012 году в веломагазине AlienBike.
                </p>
                <p>
                    В 2021 году Артем прошел обучение в Specialized Bicycle Components University в
                    Москве у Антона Круглова и получил сертификат фит-техника Retul от Specialized.
                    После этого работал фит-техником в AlienBike в Санкт-Петербурге, помогая
                    велосипедистам подобрать оптимальную посадку для комфорта и безопасности.
                </p>
                <p>
                    В последние годы Артем продолжил практику фит-техника в Velolider в Баку, а
                    сегодня в CoffeeRiders применяет накопленный опыт и современные методики
                    байкфита, чтобы райдер получал больше удовольствия и эффективности от каждой
                    поездки.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-[1120px] space-y-5">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <a
                    :href="telegramLink"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex h-14 items-center justify-center border border-foreground px-8 text-lg font-semibold transition hover:bg-foreground hover:text-background max-md:w-full md:mx-auto"
                >
                    Связаться
                </a>
            </div>

            <div
                class="grid auto-rows-[150px] grid-cols-2 gap-2 md:auto-rows-[190px] md:grid-cols-4"
            >
                <figure
                    v-for="photo in photos"
                    :key="photo.src"
                    class="overflow-hidden rounded-lg bg-muted"
                    :class="photo.className"
                >
                    <img
                        :src="photo.src"
                        :alt="photo.alt"
                        class="h-full w-full object-cover transition duration-300 hover:scale-[1.03]"
                        loading="lazy"
                    />
                </figure>
            </div>
        </section>

        <section class="mx-auto max-w-[1120px] border-t pt-8">
            <h2 class="text-left text-3xl md:text-4xl">Как проходит байкфит</h2>
            <div class="mt-5 grid gap-5 md:grid-cols-3">
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-muted-foreground">01</p>
                    <h3 class="text-xl font-semibold">Опрос и замеры</h3>
                    <p class="leading-7 text-muted-foreground">
                        Обсуждаем травмы, цели, стиль езды и снимаем углы гониометром.
                    </p>
                </div>
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-muted-foreground">02</p>
                    <h3 class="text-xl font-semibold">Настройка велосипеда</h3>
                    <p class="leading-7 text-muted-foreground">
                        Настраиваем шипы, руль, вынос, тормозные ручки, лежаки и седло.
                    </p>
                </div>
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-muted-foreground">03</p>
                    <h3 class="text-xl font-semibold">Проверка посадки</h3>
                    <p class="leading-7 text-muted-foreground">
                        Для настройки седла используем электронный или классический попомер.
                    </p>
                </div>
            </div>
        </section>

        <section class="mx-auto grid max-w-[1120px] gap-8 border-t pt-8 md:grid-cols-3">
            <div class="space-y-4">
                <h2 class="text-left text-3xl md:text-4xl">Кому подходит</h2>
                <p class="leading-7 text-muted-foreground">
                    Новичкам, любителям и опытным велосипедистам.
                </p>
            </div>

            <div class="space-y-4 md:col-span-2">
                <h2 class="text-left text-3xl md:text-4xl">С чем помогает</h2>
                <div class="flex flex-wrap gap-2">
                    <span class="border px-3 py-2 text-sm">боли в коленях</span>
                    <span class="border px-3 py-2 text-sm">боли в стопах</span>
                    <span class="border px-3 py-2 text-sm">поясница</span>
                    <span class="border px-3 py-2 text-sm">шея</span>
                    <span class="border px-3 py-2 text-sm">пах</span>
                    <span class="border px-3 py-2 text-sm">кисти</span>
                    <span class="border px-3 py-2 text-sm">плечи</span>
                </div>
            </div>
        </section>

        <section class="mx-auto grid max-w-[1120px] gap-8 border-t pt-8 md:grid-cols-[0.9fr_1.1fr]">
            <div class="space-y-4">
                <h2 class="text-left text-3xl md:text-4xl">Категории</h2>
                <p class="leading-7 text-muted-foreground">
                    Работаем с разными типами велосипедов и задачами райдера.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="border px-3 py-2 text-sm">триатлон</span>
                <span class="border px-3 py-2 text-sm">шоссе</span>
                <span class="border px-3 py-2 text-sm">гревел</span>
                <span class="border px-3 py-2 text-sm">туринг</span>
                <span class="border px-3 py-2 text-sm">фитнес</span>
                <span class="border px-3 py-2 text-sm">городские велосипеды</span>
                <span class="border px-3 py-2 text-sm">MTB XC-трейл</span>
            </div>
        </section>

        <section class="mx-auto max-w-[1120px] border-t pt-8">
            <div class="max-w-[760px] space-y-3">
                <h2 class="text-left text-3xl md:text-4xl">Важно перед встречей</h2>
                <p class="leading-7 text-muted-foreground">
                    Перед фитом нужен отдых 2 дня: если мышцы в гипертонусе после нагрузки, это
                    может помешать точной настройке посадки.
                </p>
            </div>
        </section>
    </article>
</template>
