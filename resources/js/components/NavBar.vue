<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import MobileNavMenu from '@/components/MobileNavMenu.vue';
import NavItem from '@/components/NavItem.vue';
import NavLink from '@/components/NavLink.vue';
import { Button } from '@/components/ui/button';
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuTrigger,
} from '@/components/ui/navigation-menu';
import { Separator } from '@/components/ui/separator';
import { router, usePage } from '@inertiajs/vue3';
import { Cog, LockKeyhole, LogOut, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const { t } = useI18n();

export interface MenuItem {
    children?: MenuItem[];
    icon?: any;
    buttonOnClick?: (...args: any[]) => void;
    label: string;
    show: boolean;
    url?: string;
}

const menuItems = ref<MenuItem[]>([
    {
        label: t('Weight Progress'),
        url: route('diaries'),
        show: !!user.value,
    },
    {
        label: t('Activities'),
        url: route('activities.index'),
        show: !!user.value,
    },
    {
        label: t('Meal Diary'),
        url: route('meals.index'),
        show: !!user.value,
    },

    // {
    //     label: t('Tracks'),
    //     show: true,
    //     children: [
    //         {
    //             label: t('Cycling'),
    //             url: route('tracks.cycling'),
    //             show: true,
    //         },
    //     ],
    // },
]);
console.log(user);
const dropdownItems = ref<MenuItem[]>([
    {
        label: t('Settings'),
        url: route('settings'),
        show: !!user.value,
        icon: Cog,
    },
    {
        label: t('Admin'),
        url: route('admin.home'),
        show: user.value.isAdmin,
        icon: LockKeyhole,
    },
    {
        icon: LogOut,
        label: t('Log Out'),
        buttonOnClick: () =>
            router.post(
                route('logout'),
                {},
                {
                    onSuccess: () => {
                        window.location.reload();
                    },
                },
            ),
        show: !!user.value,
    },
]);
</script>

<template>
    <nav class="bg-background absolute sticky top-0 z-10 justify-between">
        <!--     Mobile NavBar Version     -->

        <div class="grid grid-cols-3 items-center md:hidden">
            <div></div>
            <Link :href="route('home')" class="text-center">
                <AppLogoIcon class="text-center text-5xl" />
            </Link>
            <MobileNavMenu :items="[...menuItems, ...dropdownItems]" />
        </div>

        <!--     Desktop NavBar Version     -->

        <div class="hidden items-center justify-between pt-7 pb-2 md:flex">
            <Link :href="route('home')">
                <AppLogoIcon class="text-4xl" />
            </Link>
            <NavigationMenu>
                <NavigationMenuList>
                    <NavigationMenuItem v-for="item in menuItems" v-show="item.show">
                        <template v-if="item.children">
                            <NavigationMenuTrigger class="text-2xl font-bold">
                                {{ item.label }}
                            </NavigationMenuTrigger>
                            <NavigationMenuContent class="border-accent w-full text-lg">
                                <NavigationMenuLink v-for="item in item.children" as-child>
                                    <NavLink :label="item.label" :href="item.url" :icon="Cog" />
                                </NavigationMenuLink>
                            </NavigationMenuContent>
                        </template>
                        <NavigationMenuLink v-else as-child class="text-2xl font-bold">
                            <NavLink :href="item.url" :label="item.label" :icon="item.icon" />
                        </NavigationMenuLink>
                    </NavigationMenuItem>
                </NavigationMenuList>
            </NavigationMenu>

            <NavigationMenu v-if="user">
                <NavigationMenuList>
                    <NavigationMenuItem>
                        <NavigationMenuTrigger class="text-2xl font-bold">
                            <User />
                            {{ user?.name }}
                        </NavigationMenuTrigger>
                        <NavigationMenuContent class="border-accent w-full text-lg">
                            <NavItem v-for="item in dropdownItems" :item="item" :key="item.label" />
                        </NavigationMenuContent>
                    </NavigationMenuItem>
                </NavigationMenuList>
            </NavigationMenu>

            <div v-else class="flex justify-between gap-3">
                <Button size="sm" as-child>
                    <Link :href="route('login')">
                        {{ $t('Log In') }}
                    </Link>
                </Button>
                <Button variant="outline" size="sm" as-child>
                    <Link :href="route('register')">
                        {{ $t('Register') }}
                    </Link>
                </Button>
            </div>
        </div>
        <Separator class="bg-background-fallback" />
    </nav>
</template>
