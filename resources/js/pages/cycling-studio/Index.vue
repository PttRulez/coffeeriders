<script lang="ts" setup="">
import { Button } from '@/components/shadecn/button';
import { Card, CardContent, CardFooter } from '@/components/shadecn/card';
import ActionButton from '@/components/shared/ActionButton.vue';
import Modal from '@/components/shared/Modal.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import CarouselAutoplay from '@/components/shared/CarouselAutoplay.vue';
import { router } from '@inertiajs/vue3';

const buying = ref(false);
const isOpenBuyFour = ref(false);
const isOpenBuyTen = ref(false);

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
        }
    );
};

const { user } = usePage().props.auth;

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

        <div class="flex justify-center gap-5 max-md:flex-col max-md:px-10">
            <Link :href="route('cycling-studio.booking')">
                <Card class="cursor-pointer md:w-[240px]">
                    <CardContent class="text-center text-xl font-bold">
                        <p>Разовое занятие</p>
                        <p>(2 часа)</p>
                    </CardContent>
                    <CardFooter><p class="w-full text-center">{{ user.is_coffeerider ? '750': '1 500'  }} руб</p></CardFooter>
                </Card>
            </Link>

            <Modal v-model:open="isOpenBuyFour" title="Перейти на страницу банка?">
                <template #trigger>
                    <Card class="cursor-pointer md:w-[240px]">
                        <CardContent class="text-center text-xl font-bold">
                            <p>4 занятия</p>
                            <br />
                        </CardContent>
                        <CardFooter><p class="w-full text-center">5 000 руб</p></CardFooter>
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
                        <CardFooter><p class="w-full text-center">10 000 руб</p></CardFooter>
                    </Card>
                </template>

                <Button @click="buy(10)">
                    <LoaderCircle v-if="buying" class="h-4 w-4 animate-spin" />
                    Оплатить 10 000 руб
                </Button>
            </Modal>

            <Card class="md:w-[240px]">
                <CardContent class="text-center text-xl font-bold">
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
