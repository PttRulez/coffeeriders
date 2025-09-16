<script lang="ts" setup="">
import { usePage } from '@inertiajs/vue3';
import { PhoneCall } from 'lucide-vue-next';
import { formatPhone } from '../../helpers/helpers';

const page = usePage();
const { instagramLink, vkLink, phoneNumber } = page.props;
</script>

<template>
    <footer class="w-full border-t border-sidebar-border/80 bg-foreground text-background">
        <div class="mx-auto flex justify-around gap-4 p-10 md:max-w-7xl">
            <div>
                <p class="mb-5 font-bold text-center">Свяжитесь с нами:</p>
                <ul class="space-y-3">
                    <li>
                        <a :href="`tel:${phoneNumber}`" class="flex items-center gap-2">
                            <PhoneCall class="size-5 opacity-80 group-hover:opacity-100 max-md:hidden" />
                            <p class="cursor-pointer max-md:text-lg!">{{ formatPhone(phoneNumber) }}</p>
                        </a>
                    </li>
                    <li>
                        <a
                            href="https://t.me/coffeeriders_procat"
                            target="_blank"
                            class="flex items-center gap-2"
                        >
                            <i class="ri-telegram-2-line social"></i>
                            <p>- телеграм</p>
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <p class="mb-5 font-bold text-center">Сошл медиа:</p>
                <div class="flex justify-center items-center gap-2">
                    <a :href="instagramLink" target="_blank">
                        <i class="ri-instagram-line social"></i>
                    </a>
                    <a :href="vkLink" target="_blank">
                        <i class="ri-vk-fill social"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.social {
    font-size: 24px;
}
</style>
