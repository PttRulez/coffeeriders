<script  lang="ts" setup="">
import { useTypedForm } from '@/composables/useTypedForm';
import FormDatePicker from '@/components/form-elements/FormDatePicker.vue';
import FormInput from '@/components/form-elements/FormInput.vue';
import MarkdownEditor from '@/components/shared/MarkdownEditor/MarkdownEditor.vue';
import { Button } from '@/components/ui/button';

type BlogData = {
    id?: number;
    content: string;
    date: string;
    featured_img_path?: string;
    title: string;
}

type BlogFormData = {
    content: string;
    date: string;
    featured_image: File | null;
    title: string;
    _method?: string;
}

const { blog } = defineProps<{ blog?: BlogData }>();

const today = new Date().toISOString().slice(0, 10);

const form = useTypedForm<BlogFormData>(
    blog
        ? {
              content: blog.content,
              date: blog.date,
              featured_image: null,
              title: blog.title,
              _method: 'PUT',
          }
        : {
              content: '',
              date: today,
              featured_image: null,
              title: '',
          },
);

const submit = () => {
    if (blog) {
        form.post(route('adminka.blog.update', blog.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('adminka.blog.store'), {
            forceFormData: true,
        });
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="flex flex-col gap-5">
        <FormInput
            field-name="title"
            placeholder="Заголовок"
            v-model="form.title"
        />
        <FormDatePicker
                v-model="form.date"
                :error-message="form.errors.date"
                field-name="date"
                placeholder="Дата публикации"
            />
        <div>
            <FormInput
                accept="image/*"
                field-name="featured_image"
                type="file"
                class="max-w-[250px]"
                :label="blog ? 'Заменить обложку (необязательно)' : 'Обложка статьи'"
                @change="(e: Event) => form.featured_image = (e.target as HTMLInputElement).files?.[0] ?? null"
            />
            <img
                v-if="blog?.featured_img_path && !form.featured_image"
                :src="blog.featured_img_path"
                alt="Текущая обложка"
                class="mt-2 h-32 rounded-lg object-cover"
            />
        </div>
        <MarkdownEditor v-model="form.content" image-dir="blog"/>
        <Button type="submit">Сохранить</Button>
    </form>
</template>