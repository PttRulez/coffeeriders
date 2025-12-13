<script lang="ts" setup="">
import ActionButton from '@/components/shared/ActionButton.vue';
import CarouselAutoplay from '@/components/shared/CarouselAutoplay.vue';
import Modal from '@/components/shared/Modal.vue';
import RacesCard from '@/components/shared/RacesCard.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Race } from '@/types/races';
import { Head, router } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const buying = ref(false);
const isOpenBuyFour = ref(false);
const isOpenBuyTen = ref(false);
const props = defineProps<{
    price: number;
    races: Race[];
}>();
const buy = (quantity: number) => {
    buying.value = true;

    router.post(
        route('cycling-studio.buy-activities'),
        { quantity },
        {
            onStart: () => {
                buying.value = true;
            },
            onFinish: () => {
                buying.value = false;
            },
            onError: (errors) => {
                console.error('Ошибка при создании платежа:', errors);
            },
        },
    );
};

const bannerImages = [
    {
        url: '/img/pages/studio/banner_1.jpg',
        alt: 'hello',
    },
    {
        url: '/img/pages/studio/banner_2.jpg',
        alt: 'hello',
    },
];
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

        <CarouselAutoplay :images="bannerImages" :hideArrows="true" />

        <div class="flex justify-center items-stretch gap-5 max-md:flex-col max-md:px-10">
            <Link :href="route('cycling-studio.create')">
                <Card class="cursor-pointer md:w-[240px]">
                    <CardContent class="text-center text-xl font-bold">
                        <p>Разовое занятие</p>
                        <p>(2 часа)</p>
                    </CardContent>
                    <CardFooter class="place-content-center">
                        <Button class="max-w-[90%] p-6 text-center text-xl text-wrap">
                            <template v-if="props.price">{{ props.price }} руб.</template>
                            <template v-else>Забронировать</template>
                        </Button>
                        <Link :href="route('cycling-studio.create')" v-if="props.price"> </Link>
                    </CardFooter>
                </Card>
            </Link>

            <Modal v-model:open="isOpenBuyFour" title="Перейти на страницу банка?">
                <template #trigger>
                    <Card class="cursor-pointer md:w-[240px]">
                        <CardContent class="text-center text-xl font-bold">
                            <p>4 занятия</p>
                            <br />
                        </CardContent>
                        <CardFooter class="place-content-center">
                            <Button class="p-6 text-center text-xl"> 5000 руб </Button>
                        </CardFooter>
                    </Card>
                </template>

                <Button @click="buy(4)">
                    <LoaderCircle v-if="buying" class="h-4 w-4 animate-spin" />
                    Оплатить 5 000 руб
                </Button>
            </Modal>

            <Modal v-model:open="isOpenBuyTen" title="Перейти на страницу банка?">
                <template #trigger>
                    <Card class="cursor-pointer md:w-[240px]">
                        <CardContent class="text-center text-xl font-bold">
                            <p>10 занятий</p>
                            <br />
                        </CardContent>
                        <CardFooter class="place-content-center">
                            <Button class="p-6 text-center text-xl"> 10 000 руб </Button>
                        </CardFooter>
                    </Card>
                </template>

                <Button @click="buy(10)">
                    <LoaderCircle v-if="buying" class="h-4 w-4 animate-spin" />
                    Оплатить 10 000 руб
                </Button>
            </Modal>
        </div>

        <div class="flex justify-center">
            <ActionButton :href="route('cycling-studio.create')" text="Забронировать" />
        </div>

        <RacesCard :races="props.races" v-if="props.races.length > 0" class="mx-auto"/>

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
