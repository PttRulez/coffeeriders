<script lang="ts" setup="">
import { Card, CardContent, CardFooter } from '@/components/shadecn/card';
import { Head } from '@inertiajs/vue3';
import ActionButton from '@/components/ActionButton.vue';
</script>

<template>
    <Head title="Велостудия в СПб – тренировки на Zwift-байках и шоссейных велосипедах">
        <meta
            name="description"
            content="Возьми велосипед напрокат в Санкт-Петербурге: шоссейные, гравийные и МТБ модели. Прокат от 2500₽ в день. Забирай у метро Удельная — без лишних заморочек."
        />
    </Head>

    <div class="space-y-5">
        <h1>Велостудия</h1>

        <img src="/img/pages/studio/zwift-studio.jpg" />

        <div class="flex max-md:flex-col max-md:px-10 justify-center gap-5">
            <Card class="md:w-[240px]">
                <CardContent class="text-xl font-bold text-center">
                    <p>Разовое занятие</p>
                    <p>(2 часа)</p>
                </CardContent>
                <CardFooter><p class="w-full text-center">1 500 руб</p></CardFooter>
            </Card>

            <Card class="md:w-[240px]">
                <CardContent class="text-xl font-bold text-center">
                    <p>4 занятия</p>
                    <br />
                </CardContent>
                <CardFooter><p class="w-full text-center">5 000 руб</p></CardFooter>
            </Card>

            <Card class="md:w-[240px]">
                <CardContent class="text-xl font-bold text-center">
                    <p>10 занятий</p>
                    <br />
                </CardContent>
                <CardFooter><p class="w-full text-center">10 000 руб</p></CardFooter>
            </Card>

            <Card class="md:w-[240px]">
                <CardContent class="text-xl font-bold text-center">
                    <p>Безлимит на месяц</p>
                    <br />
                </CardContent>
                <CardFooter><p class="w-full text-center">12 000 руб</p></CardFooter>
            </Card>
        </div>
        <div class="flex justify-center">
        <ActionButton :href="route('cycling-studio.booking')" text="Забронировать" />
            </div>
        <p>
            Наша велостудия — это больше, чем просто тренировки. Это сообщество единомышленников,
            объединённых любовью к активному образу жизни. Позвоните нам или оставьте заявку на
            сайте, чтобы узнать подробности и забронировать своё место на тренировке. Ваше здоровье
            и красота — наш приоритет!
        </p>
        <p>
            В холодное время вело любители выбирают тренировки в зале вместо уличных поездок. В
            Санкт-Петербурге наша велостудия предлагает занятия на современных станках Tacx Neo 2T и
            Zwift байках.
        </p>
    </div>
</template>
