<script setup lang="ts">
import CarouselAutoplay from '@/components/CarouselAutoplay.vue';
import ActionButton from '@/components/ActionButton.vue';
import { Separator } from '@/components/shadecn/separator';

const bannerImages = [
    {
        url: '/img/pages/home/main-banner/1.jpg',
        alt: 'hello',
    },
    {
        url: '/img/pages/home/main-banner/2.jpg',
        alt: 'hello',
    },
    {
        url: '/img/pages/home/main-banner/3.jpg',
        alt: 'hello',
    },
    {
        url: '/img/pages/home/main-banner/4.jpg',
        alt: 'hello',
    },
];

const teamImages = [
    {
        url: '/img/pages/home/team/1.jpg',
        alt: 'hello',
    },
    {
        url: '/img/pages/home/team/2.jpg',
        alt: 'hello',
    },
    {
        url: '/img/pages/home/team/3.jpg',
        alt: 'hello',
    },
    {
        url: '/img/pages/home/team/4.jpg',
        alt: 'hello',
    },

    {
        url: '/img/pages/home/team/5.jpg',
        alt: 'hello',
    },
];

</script>

<template>
    <Head title="Любительская велокоманда из Санкт-Петербурга">
        <meta
            name="description"
            content="Coffeeriders team — это любительская команда, зародившаяся в недрах Питера на
                    Неве в 2023 году для того, чтобы объединить вокруг себя поклонников велоспорта."
        />
    </Head>

    <div class="space-y-10">
        <CarouselAutoplay :images="bannerImages" :hideArrows="true" />

        <section class="items-center gap-10 md:flex md:flex-row-reverse">
            <div class="min-w-[30%] space-y-5">
                <p>
                    Coffeeriders team — это любительская команда, зародившаяся в недрах Питера на
                    Неве в 2023 году для того, чтобы объединить вокруг себя поклонников велоспорта.
                </p>
                <p>
                    Наш клуб - это место, где ты можешь стать частью команды, начать активно
                    развиваться в велоспорте под руководством людей, знающих что нужно
                    велосипедистам.
                </p>
                <p>Мы подготовим всё для вас и вашего роста</p>
            </div>
        </section>

        <Separator />

        <section class="space-y-10">
            <h2>Наши тренеры</h2>

            <!--      Игорь          -->
            <div class="flex gap-5 max-md:flex-col-reverse justify-between  items-center">
                <p class="italic">"1500 ватт - это только начало интервала"</p>
                <div class="flex flex-col items-center gap-2">
                    <img
                        class="max-w-[240px] rounded-full"
                        src="/img/pages/home/igor_petrov.jpg"
                        alt="Игорь Петров"
                    />
                    <div class="text-lg!">
                        <p class="text-center text-lg! font-bold">Игорь Петров</p>
                        <p class="text-center text-lg!">МС по шоссейному велоспорту</p>
                    </div>
                </div>
            </div>

            <!--      Егор          -->
            <div class="flex gap-5 max-md:flex-col items-center">
                <div class="flex flex-col items-center gap-2 md:gap-10">
                    <img
                        class="max-w-[240px] rounded-full"
                        src="/img/pages/home/mash.jpg"
                        alt="Егор Маш"
                    />
                    <div class="text-lg!">
                        <p class="text-center text-lg! font-bold">Егор Маш</p>
                        <p class="text-center text-lg!">МС по велоспорту</p>
                    </div>
                </div>
                <p class="italic">" Всем привет. Являюсь Мастером спорта по велоспорту (мтб), Чемпионом России
                            и многократным победителем и призером Первенств России, всероссийских и международных соревнований,
                            в прошлом член Сборной команды России. Расскажу и научу вас всем тонкостям и навыкам мтб и шоссе"</p>
            </div>
        </section>

        <Separator />

        <section class="space-y-5">
            <h2>Вступайте в команду</h2>

            <div class="flex gap-10 max-md:flex-col">
                <div class="md:max-w-[50%]">
                    <CarouselAutoplay :images="teamImages" :hideArrows="true" />
                </div>
                <div class="flex flex-col gap-5">
                    <p>
                        Члены команды получают возможность быть участником всех наших событий с
                        привилегиями, скидками и командой атмосферой.
                    </p>
                    <p>Каждый участник получает скидку на все наши продукты:</p>
                    <ul class="list-inside list-disc pl-5">
                        <li>Тренировки</li>
                        <li>Кемпы</li>
                        <li>Вело мастерскую</li>
                        <li>Вело студию</li>
                        <li>Прокат велосипедов</li>
                        <li>Скидку на слоты соревнований</li>
                        <li>Приятности от партнеров</li>
                    </ul>
                </div>
            </div>
        </section>

        <Separator />

        <section class="space-y-5">
            <h2>Велостудия</h2>
            <div class="flex flex-col items-center gap-5 md:float-left md:mr-10 md:max-w-[50%]">
                <img src="/img/pages/studio/zwift-studio.jpg" />
                <ActionButton :href="route('cycling-studio.index')" />
            </div>
            <p>
                Чтобы быть полноценной экосистемой, мы открыли
                <Link :href="route('cycling-studio.index')" class="text-blue-400">велостудию</Link>
                . Благодаря этому ваш тренировочный процесс не будет прерываться в течении года, не
                смотря на погоду.
            </p>

            <p>
                Мы считаем, что это идеальное место, в котором каждый сможет найти свой путь к
                прогрессу в спорте или получения удовольствия от спорта.
            </p>
            <p>
                Попробуйте новую грань велоспорта на пробном занятии. Позвоните нам или оставьте
                заявку на сайте, чтобы узнать подробности и забронировать своё место на тренировке.
                Наши приоритеты — ваш прогресс и удовольствие.
            </p>
        </section>

        <Separator />

        <section class="space-y-5">
            <h2>Прокат велосипедов</h2>
            <div class="flex gap-10 max-md:flex-col">
                <img src="/img/pages/home/rent.jpg" class="md:max-w-[50%]" />
                <div class="flex flex-col items-center gap-5">
                    <p>
                        Coffeeriders предлагает
                        <Link :href="route('rent-bikes.index')" class="text-blue-400">
                            аренду велосипедов в Санкт-Петербурге
                        </Link>
                        с договором — прозрачно, оперативно и по-человечески. У нас большой выбор
                        велосипедов под разные задачи: гонки, тренировки, коферайды или активные
                        досуг в приятной компании. Мы следим за сервисом: быстро отвечаем, не
                        усложняем процесс и всегда на связи. Все модели проходят регулярное
                        техническое обслуживание и укомплектованы всем необходимым.
                    </p>
                    <ActionButton :href="route('rent-bikes.index')" />
                </div>
            </div>
        </section>

        <Separator />

        <section class="space-y-5">
            <h2>Веломастерская</h2>
            <p>
                Качественный велоспорт невозможен без квалифицированного и своевременного
                обслуживания велосипеда. Мы собрали команду лучших веломехаников, к которым вы
                можете обратиться по любому вопросу: от простой диагностики до комплексной
                реставрации — мы выполняем полный спектр работ.
            </p>
            <p>Профессиональный ремонт велосипедов — наша специализация!</p>
        </section>
    </div>
</template>

<style scoped>
[data-slot='carousel-next'] {
    right: 50px !important;
}

h1 {
    font-size: var(--text-3xl);
    line-height: var(--tw-leading, var(--text-3xl--line-height));
}

h2 {
    font-size: var(--text-3xl);
    line-height: var(--tw-leading, var(--text-3xl--line-height));
}

h3 {
    font-size: var(--text-2xl);
    line-height: var(--tw-leading, var(--text-2xl--line-height)) font;
}

@media (min-width: 48rem) {
    p {
        font-size: var(--text-2xl);
        line-height: var(--tw-leading, var(--text-2xl--line-height));
    }
}
</style>
