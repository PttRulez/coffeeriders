<script lang="ts" setup="">
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const { instagramLink, telegramLink, vkLink } = page.props;
</script>

<template>
    <h1 class="mb-10 text-center">Контакты</h1>
    <div class="md:grid md:grid-cols-2 max-md:flex max-md:flex-col gap-20">
        <iframe
            src="https://yandex.ru/map-widget/v1/?z=12&ol=biz&oid=173215367837"
            class="w-full aspect-square flex-1"
            frameborder="0"
        ></iframe>
        <div class="flex-1">
            <section class="mb-5">
                <h2>Наш адрес:</h2>
                <p>Санкт-Петербург. Ярославский пр, 32</p>
            </section>

            <section>
                <h2>Наши соцсети</h2>
                <div class="flex items-center gap-2">
                    <a :href="instagramLink" target="_blank">
                        <i class="ri-instagram-line social"></i>
                    </a>
                    <a :href="vkLink" target="_blank">
                        <i class="ri-vk-fill social"></i>
                    </a>
                    <a :href="telegramLink" target="_blank">
                        <i class="ri-telegram-2-line social"></i>
                    </a>
                </div>
            </section>

            <section>
                <h2>Реквизиты компании</h2>
                <p>ИП Васильев Роман Сергеевич</p>
                <p><strong>ОГРН:</strong> 325784700298537</p>
                <p><strong>ИНН:</strong> 782000759264</p>
            </section>
        </div>
    </div>
</template>
<style scoped>
.social {
    font-size: 48px;
}
</style>
