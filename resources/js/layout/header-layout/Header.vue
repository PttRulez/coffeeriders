<script setup lang="ts">
import TelegramIcon from '@/components/icons/TelegramIcon.vue';
import { Button } from '@/components/ui/button';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import AppLogo from '@/layout/components/AppLogo.vue';
import { getNavItems } from '@/layout/header-layout/nav-items';
import type { NavItem } from '@/types';
import { Role } from '@/types/enums';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown, Menu, PhoneCall } from 'lucide-vue-next';
import { computed, getCurrentInstance } from 'vue';
import { formatPhone } from '../../helpers';

const page = usePage();

const activeItemStyles = computed(
    () => (url: string) =>
        isCurrentRoute.value(url)
            ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100'
            : '',
);
const { auth, phoneNumber, telegramLink } = page.props;
const isCurrentRoute = computed(() => (url: string) => page.url === url);
const isAdmin = computed(() => auth.user?.role === Role.Admin);
const isAuthenticated = computed(() => Boolean(auth.user));
const isAdminPanel = computed(() => page.url.includes('adminka'));

const routeFn = getCurrentInstance()!.appContext.config.globalProperties.route;

const mobileNavItems = computed((): NavItem[] =>
    getNavItems({
        device: 'mobile',
        isAdmin: isAdmin.value,
        isAuthenticated: isAuthenticated.value,
        isAdminPanel: isAdminPanel.value,
        route: routeFn
    }),
);

const desktopNavItems = computed((): NavItem[] =>
    getNavItems({
        device: 'desktop',
        isAdmin: isAdmin.value,
        isAuthenticated: isAuthenticated.value,
        isAdminPanel: isAdminPanel.value,
        route: routeFn
    }),
);
</script>

<template>
    <nav class="fixed z-10 w-full bg-background">
        <div
            class="my-container flex h-16 flex-1 items-center justify-between gap-4 border-b border-sidebar-border/80 px-1 md:px-10"
        >
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
<!--                        <SheetHeader class="flex justify-start text-left ">-->
<!--                            <SheetClose as-child>-->
<!--                                <Link :href="route('home')">-->
<!--                                    <AppLogoIcon-->
<!--                                        class="fill-current text-black dark:text-white"-->
<!--                                    />-->
<!--                                </Link>-->
<!--                            </SheetClose>-->
<!--                        </SheetHeader>-->
                        <div class="flex h-full min-h-0 flex-1 flex-col py-6">
                            <nav class="-mx-3 min-h-0 flex-1 space-y-1 overflow-y-auto pr-1">
                                <template v-for="item in mobileNavItems" :key="item.title">
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
                                    <template v-if="item.children?.length">
                                        <template v-for="i in item.children" :key="i.title">
                                            <SheetClose as-child>
                                                <Link
                                                    v-if="item.show"
                                                    :href="i.href"
                                                    class="ml-9 flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                                                    :class="activeItemStyles(i.href)"
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
                                    </template>
                                </template>
                            </nav>
                        </div>
                    </SheetContent>
                </Sheet>
            </div>

            <Link
                :href="route('home')"
                class="font-ze text-center text-2xl text-purple-900 lg:hidden"
            >
                CoffeeRiders
            </Link>
            <Link
                :href="route('home')"
                class="hidden text-center text-2xl text-purple-900 lg:block"
            >
                <AppLogo />
            </Link>

            <!-- Desktop Menu -->
            <div class="hidden h-full lg:flex lg:flex-1">
                <NavigationMenu class="ml-10 flex h-full items-stretch">
                    <NavigationMenuList class="flex h-full items-stretch gap-0">
                        <template v-for="(item, index) in desktopNavItems" :key="index">
                            <NavigationMenuItem
                                v-if="item.show"
                                class="relative flex h-full items-center"
                            >
                                <template v-if="item.children?.length">
                                    <div
                                        class="group/menu relative flex h-full items-center"
                                        tabindex="0"
                                    >
                                        <Link
                                            :class="[
                                                navigationMenuTriggerStyle(),
                                                activeItemStyles(item.href),
                                                'flex h-9 cursor-pointer items-center px-3 text-[18px]',
                                            ]"
                                            :href="item.href"
                                        >
                                            <component
                                                v-if="item.icon"
                                                :is="item.icon"
                                                class="mr-2 h-6"
                                            />
                                            {{ item.title }}
                                            <ChevronDown class="ml-1 h-4 w-4" />
                                        </Link>

                                        <ul
                                            class="invisible absolute top-full left-0 z-20 mt-1 min-w-full rounded-md border bg-popover p-1 opacity-0 shadow transition-all group-focus-within/menu:visible group-focus-within/menu:opacity-100 group-hover/menu:visible group-hover/menu:opacity-100"
                                        >
                                            <li
                                                v-for="subitem in item.children"
                                                :key="subitem.title"
                                            >
                                                <Link
                                                    :class="[
                                                        navigationMenuTriggerStyle(),
                                                        activeItemStyles(subitem.href),
                                                        'flex h-9 w-full! cursor-pointer px-3 text-[18px] whitespace-nowrap',
                                                    ]"
                                                    :href="subitem.href"
                                                >
                                                    <component
                                                        v-if="subitem.icon"
                                                        :is="subitem.icon"
                                                        class="mr-2 h-6"
                                                    />
                                                    {{ subitem.title }}
                                                </Link>
                                            </li>
                                        </ul>
                                    </div>
                                </template>
                                <template v-else>
                                    <Link
                                        :class="[
                                            navigationMenuTriggerStyle(),
                                            activeItemStyles(item.href),
                                            'h-9 cursor-pointer px-3 text-[18px]',
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
                                </template>
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
                    <a :href="`tel:${phoneNumber}`" class="flex items-center md:hidden">
                        <Button variant="ghost" size="icon" class="group cursor-pointer">
                            <PhoneCall class="size-5 opacity-80 group-hover:opacity-100" />
                        </Button>
                        <p class="hidden">{{ phoneNumber }}</p>
                    </a>
                    <a :href="`tel:${phoneNumber}`" class="flex items-center max-md:hidden">
                        <p class="cursor-pointer text-[18px]">{{ formatPhone(phoneNumber) }}</p>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</template>
