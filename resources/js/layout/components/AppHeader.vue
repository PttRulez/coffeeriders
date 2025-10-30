<script setup lang="ts">
import TelegramIcon from '@/components/icons/TelegramIcon.vue';
import { Button } from '@/components/shadecn/button';
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuList,
    NavigationMenuTrigger,
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
import type { BreadcrumbItem, NavItem } from '@/types';
import { BikeCategory, Role } from '@/types/enums';
import { Link, usePage } from '@inertiajs/vue3';
import { Bike, FolderKanban, Menu, PhoneCall } from 'lucide-vue-next';
import { computed } from 'vue';
import { formatPhone } from '../../helpers';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const page = usePage();

const activeItemStyles = computed(
    () => (url: string) =>
        isCurrentRoute.value(url)
            ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100'
            : '',
);
const { auth, phoneNumber, telegramLink } = page.props;
const isCurrentRoute = computed(() => (url: string) => page.url === url);

const navItems = computed((): NavItem[] =>
    page.url.includes('adminka')
        ? [
              {
                  title: 'Прокат',
                  show: true,
                  href: '',
                  children: [
                      {
                          href: route('adminka.rent-bikes.index'),
                          icon: Bike,
                          title: 'Велики',
                          show: true,
                      },
                      {
                          title: 'Бронь',
                          href: route('adminka.rent-bikes.bookings'),
                          icon: FolderKanban,
                          show: true,
                      },
                  ],
              },
              {
                  title: 'Студия',
                  show: true,
                  href: '',
                  children: [
                      {
                          href: route('adminka.cycling-studio.index'),
                          title: 'Бронирования',
                          show: true,
                      },
                  ],
              },
              {
                  title: 'Админка',
                  href: route('adminka.index'),
                  show: auth.user?.role === Role.Admin,
              },
          ]
        : [
              {
                  title: 'Аренда',
                  href: route('rent-bikes.index'),
                  icon: Bike,
                  show: true,
                  children: [
                      {
                          title: 'Шоссер',
                          href: route('rent-bikes.category', BikeCategory.Road),
                          show: true,
                      },
                      {
                          title: 'Гравийные',
                          href: route('rent-bikes.category', BikeCategory.Gravel),
                          show: true,
                      },
                      {
                          title: 'МТБ',
                          href: route('rent-bikes.category', BikeCategory.MTB),
                          show: true,
                      },
                  ],
              },
              {
                  title: 'Студия',
                  href: route('cycling-studio.index'),
                  show: true,
              },
              {
                  title: 'Контакты',
                  href: route('contacts'),
                  show: true,
              },
              {
                  title: 'Мой аккаунт',
                  href: route('user-account.index'),
                  show: !!auth.user,
              },
              {
                  title: 'Войти',
                  href: route('login'),
                  show: !auth.user,
              },
              {
                  title: 'Регистрация',
                  href: route('register'),
                  show: !auth.user,
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
                        <SheetHeader class="flex justify-start text-left">
                            <SheetClose as-child>
                                <Link :href="route('home')">
                                    <AppLogoIcon
                                        class="size-6 fill-current text-black dark:text-white"
                                    />
                                </Link>
                            </SheetClose>
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
                                    <template v-if="item.children?.length">
                                        <template v-for="item in item.children">
                                            <SheetClose as-child>
                                                <Link
                                                    v-if="item.show"
                                                    :href="item.href"
                                                    class="ml-9 flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
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
                Coffeeriders
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
                    <NavigationMenuList class="flex h-full items-stretch space-x-2">
                        <template v-for="(item, index) in navItems" :key="index">
                            <NavigationMenuItem
                                v-if="item.show"
                                class="relative flex h-full items-center"
                            >
                                <template v-if="item.children?.length">
                                    <NavigationMenuTrigger
                                        :class="[
                                            navigationMenuTriggerStyle(),
                                            activeItemStyles(item.href),
                                            'h-9 px-3 text-xl',
                                        ]"
                                    >
                                        <Link :href="item.href" class="flex">
                                            <component
                                                v-if="item.icon"
                                                :is="item.icon"
                                                class="mr-2 h-6"
                                            />
                                            {{ item.title }}
                                        </Link>
                                    </NavigationMenuTrigger>
                                    <NavigationMenuContent>
                                        <ul>
                                            <li
                                                v-for="subitem in item.children"
                                                :key="subitem.title"
                                            >
                                                <Link
                                                    :class="[
                                                        navigationMenuTriggerStyle(),
                                                        activeItemStyles(subitem.href),
                                                        'flex h-9 w-full! cursor-pointer px-3 text-xl',
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
                                    </NavigationMenuContent>
                                </template>
                                <template v-else>
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
                        <p class="cursor-pointer">{{ formatPhone(phoneNumber) }}</p>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</template>
