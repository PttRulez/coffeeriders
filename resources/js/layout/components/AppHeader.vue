<script setup lang="ts">
import TelegramIcon from '@/components/icons/TelegramIcon.vue';
import { Button } from '@/components/shadecn/button';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/shadecn/navigation-menu';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/shadecn/sheet';
import AppLogo from '@/layout/components/AppLogo.vue';
import AppLogoIcon from '@/layout/components/AppLogoIcon.vue';
import Breadcrumbs from '@/layout/components/Breadcrumbs.vue';
import type { BreadcrumbItem, NavItem } from '@/types';
import { Role } from '@/types/enums';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Bike, FolderKanban, Menu, PhoneCall } from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();

const activeItemStyles = computed(
    () => (url: string) =>
        isCurrentRoute.value(url)
            ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100'
            : '',
);
const { auth, phoneNumber, telegramLink } = page.props;
const isCurrentRoute = computed(() => (url: string) => page.url === url);

const navItems = computed<NavItem[]>(() =>
    page.url.includes('adminka')
        ? [
              {
                  title: 'Велики',
                  href: route('adminka.rent-bikes.index'),
                  icon: Bike,
                  show: true,
              },
              {
                  title: 'Бронь',
                  href: route('adminka.index'),
                  icon: FolderKanban,
                  show: auth.user?.role === Role.Admin,
              },
              {
                  title: 'Сайт',
                  href: route('home'),
                  show: true,
              },
          ]
        : [
              {
                  title: 'Аренда',
                  href: route('rent-bikes.index'),
                  icon: Bike,
                  show: true,
              },
              {
                  title: 'Админка',
                  href: route('adminka.index'),
                  show: auth.user?.role === Role.Admin,
              },
          ],
);
</script>

<template>
    <div class="fixed z-10 w-full bg-background">
        <div class="border-b border-sidebar-border/80">
            <div class="mx-auto flex h-16 items-center justify-between gap-4 px-4 md:max-w-7xl">
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger as-child>
                            <Button variant="ghost" size="icon" class="mr-2 h-9 w-9">
                                <Menu class="size-20 h-6 w-6" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-[300px] p-6">
                            <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
                            <SheetHeader class="flex justify-start text-left">
                                <AppLogoIcon
                                    class="size-6 fill-current text-black dark:text-white"
                                />
                            </SheetHeader>
                            <div class="flex h-full flex-1 flex-col justify-between space-y-4 py-6">
                                <nav class="-mx-3 space-y-1">
                                    <template v-for="item in navItems" :key="item.title">
                                        <SheetClose as-child>
                                            <Link
                                                v-if="item.show"
                                                :href="item.href"
                                                class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                                                :class="activeItemStyles(item.href)"
                                            >
                                                <component
                                                    v-if="item.icon"
                                                    :is="item.icon"
                                                    class="h-5 w-5"
                                                />
                                                {{ item.title }}
                                            </Link>
                                        </SheetClose>
                                    </template>
                                </nav>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <Link
                    :href="route('home')"
                    class="font-zefani text-center text-2xl text-purple-900 lg:hidden"
                >
                    Coffee Riders
                </Link>
                <AppLogo class="hidden lg:block" />
                <!-- Desktop Menu -->
                <div class="hidden h-full lg:flex lg:flex-1">
                    <NavigationMenu class="ml-10 flex h-full items-stretch">
                        <NavigationMenuList class="flex h-full items-stretch space-x-2">
                            <template v-for="(item, index) in navItems" :key="index">
                                <NavigationMenuItem
                                    v-if="item.show"
                                    class="relative flex h-full items-center"
                                >
                                    <Link
                                        :class="[
                                            navigationMenuTriggerStyle(),
                                            activeItemStyles(item.href),
                                            'h-9 cursor-pointer px-3 text-xl',
                                        ]"
                                        :href="item.href"
                                    >
                                        <component
                                            v-if="item.icon"
                                            :is="item.icon"
                                            class="mr-2 h-6"
                                        />
                                        {{ item.title }}
                                    </Link>
                                    <div
                                        v-if="isCurrentRoute(item.href)"
                                        class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-black dark:bg-white"
                                    ></div>
                                </NavigationMenuItem>
                            </template>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <div class="flex justify-end space-x-2">
                    <div class="relative flex items-center space-x-4">
                        <a :href="telegramLink" class="flex items-center" target="_blank">
                            <TelegramIcon class="size-6" />
                        </a>
                        <a :href="`tel:${phoneNumber}`" class="flex items-center">
                            <Button variant="ghost" size="icon" class="group cursor-pointer">
                                <PhoneCall class="size-5 opacity-80 group-hover:opacity-100" />
                            </Button>
                            <p class="hidden">{{ phoneNumber }}</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="props.breadcrumbs.length > 1"
            class="flex w-full border-b border-sidebar-border/70"
        >
            <div
                class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl"
            >
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
