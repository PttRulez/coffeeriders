<script setup lang="ts">
import { Iframe } from '@/extension/iframe';
import Youtube from '@/extension/youtube';
import ResizableImageExtension from 'tiptap-extension-resize-image';
import TextAlign from '@tiptap/extension-text-align';
import { StarterKit } from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { onMounted, ref, watch } from 'vue';
import ImageModal from '@/components/shared/MarkdownEditor/ImageModal.vue';
import axios from 'axios';
import { TextSelection, NodeSelection } from '@tiptap/pm/state';

const props = defineProps<{
    imageDir?: string;
}>();

const model = defineModel();
const openImgModal = ref(false);

const editor = useEditor({
    extensions: [
        StarterKit.configure({
            code: false,
            codeBlock: false,
            heading: {
                levels: [2, 3, 4],
            },
        }),
        Youtube,
        Iframe,
        ResizableImageExtension.configure({
            inline: true,
        }),
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
    ],
    editorProps: {
        attributes: {
            class: `prose prose-sm max-w-none py-1.5 px-3 prose-img:my-0`,
        },
        handleKeyDown: (view, event) => {
            const { selection } = view.state;
            if(selection instanceof NodeSelection) {
            console.log(selection.node.type.name)

            }
            if (
                selection instanceof NodeSelection &&
                selection.node.type.name === 'imageResize'
            ) {
                console.log(selection.node.type.name)
                // Delete/Backspace — удаляем изображение с сервера
                if (event.key === 'Backspace' || event.key === 'Delete') {
                    const src = selection.node.attrs.src;
                    if (src && src.startsWith('/storage/')) {
                        deleteImage(src);
                    }
                    return false;
                }

                // Печатный символ — переносим курсор после изображения, символ вводится туда
                if (event.key.length === 1 && !event.ctrlKey && !event.metaKey) {
                    const $pos = view.state.doc.resolve(selection.to);
                    view.dispatch(
                        view.state.tr.setSelection(TextSelection.near($pos, 1)),
                    );
                    return false;
                }

                // Enter — переносим курсор после изображения
                if (event.key === 'Enter') {
                    const $pos = view.state.doc.resolve(selection.to);
                    view.dispatch(
                        view.state.tr.setSelection(TextSelection.near($pos, 1)),
                    );
                    return false;
                }
            }

            return false;
        },
    },
    onUpdate: () => {
        model.value = editor.value?.getHTML();
    },
});

const addYoutube = () => {
    const url = prompt('Вставь ссылку на YouTube');
    const match = url?.match(/(?:youtube\.com.*(?:\?|&)v=|youtu\.be\/)([\w\-]+)/);
    const videoId = match?.[1];
    if (videoId) {
        editor.value?.commands.insertContent({
            type: 'youtube',
            attrs: { videoId },
        });
    } else {
        alert('Не удалось определить videoId');
    }
};

const addAnchorLink = () => {
    if (!editor.value) return;

    if (editor.value.isActive('link')) {
        editor.value.chain().focus().unsetLink().run();
        return;
    }

    const href = prompt('Введите ссылку');

    if (!href) return;

    editor.value
        .chain()
        .focus()
        .extendMarkRange('link') // <-- важно для перезаписи существующих ссылок
        .setLink({ href })
        .run();
};

const addImage = async (imageData: { url?: string; file?: File }) => {
    if (!editor.value) return;

    try {
        let imageUrl: string;

        if (imageData.url) {
            // Если URL, используем как есть
            imageUrl = imageData.url;
        } else if (imageData.file) {
            // ← ЭТО ИЗМЕНИЛОСЬ: теперь загружаем на сервер вместо base64
            imageUrl = await uploadImage(imageData.file);
        } else {
            return;
        }

        // Вставляем изображение с полученным URL
        editor.value.chain().focus().setImage({ src: imageUrl }).run();
        openImgModal.value = false;
    } catch (_) {
        alert('Не удалось загрузить изображение');
    }
};

const toggleCenter = () => {
    if (!editor.value) return;
    const { selection } = editor.value.state;

    if (selection instanceof NodeSelection && selection.node.type.name === 'imageResize') {
        const attrs = {
            ...selection.node.attrs,
            wrapperStyle: 'display: block;',
            containerStyle: selection.node.attrs.containerStyle
                .replace(/float:\s*\w+;?\s*/g, '')
                .replace(/display:\s*inline-block;?/g, '')
                .replace(/padding-(left|right):\s*\w+;?\s*/g, '')
                + ' display: block; margin: 0 auto;',
        };
        editor.value.view.dispatch(
            editor.value.state.tr.setNodeMarkup(selection.from, null, attrs),
        );
        return;
    }

    editor.value.chain().focus().toggleTextAlign('center').run();
};

const deleteImage = async (url: string) => {
    try {
        await axios.delete(route('adminka.image.destroy'), { data: { url } });
    } catch (error) {
        console.error('Ошибка удаления изображения:', error);
    }
};

const uploadImage = async (file: File): Promise<string> => {
    const formData = new FormData();
    formData.append('image', file);
    if (props.imageDir) {
        formData.append('dir', props.imageDir);
    }

    try {
        const response = await axios.post(route('adminka.image.upload'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        console.log('MarkdownEditor.uploadImage response.data:', response.data);
        return response.data.url;
    } catch (error) {
        console.error('Ошибка загрузки изображения:', error);
        throw error;
    }
};

onMounted(() => {
    watch(
        model,
        (value: any) => {
            if (value === editor.value?.getHTML()) {
                return;
            }
            editor.value?.commands.setContent(value);
        },
        { immediate: true },
    );
});
</script>

<template>
    <div
        v-if="editor"
        class="flex aspect-video flex-col rounded-md border-0 bg-white shadow-sm ring-1 ring-gray-300 ring-inset focus:ring-[3px] focus:ring-indigo-600 focus:ring-inset focus-visible:ring-[3px]"
    >
        <menu class="flex flex-wrap divide-x border-b">
            <!--    BOLD        -->
            <li>
                <button
                    @click="() => editor.chain().focus().toggleBold().run()"
                    type="button"
                    class="cursor-pointer rounded-tl-md px-3 py-2"
                    :class="[
                        editor.isActive('bold') ? 'bg-primary text-background' : 'hover:bg-accent',
                    ]"
                    title="Bold"
                >
                    <i class="ri-bold" />
                </button>
            </li>
            <!--    ITALIC        -->
            <li>
                <button
                    @click="() => editor.chain().focus().toggleItalic().run()"
                    type="button"
                    class="cursor-pointer px-3 py-2"
                    :class="[
                        editor.isActive('italic')
                            ? 'bg-primary text-background'
                            : 'hover:bg-accent',
                    ]"
                    title="Italic"
                >
                    <i class="ri-italic" />
                </button>
            </li>
            <!--    STRIKE        -->
            <li>
                <button
                    @click="() => editor.chain().focus().toggleStrike().run()"
                    type="button"
                    class="cursor-pointer px-3 py-2"
                    :class="[
                        editor.isActive('strike')
                            ? 'bg-primary text-background'
                            : 'hover:bg-accent',
                    ]"
                    title="Italic"
                >
                    <i class="ri-strikethrough-2" />
                </button>
            </li>
            <!--    BLOCKQUOTE        -->
            <li>
                <button
                    @click="() => editor.chain().focus().toggleBlockquote().run()"
                    type="button"
                    class="cursor-pointer px-3 py-2"
                    :class="[
                        editor.isActive('blockquote')
                            ? 'bg-primary text-background'
                            : 'hover:bg-accent',
                    ]"
                    title="Blockquote"
                >
                    <i class="ri-double-quotes-l" />
                </button>
            </li>
            <!--    BULLET_LIST        -->
            <li>
                <button
                    @click="() => editor.chain().focus().toggleBulletList().run()"
                    type="button"
                    class="cursor-pointer px-3 py-2"
                    :class="[
                        editor.isActive('bulletList')
                            ? 'bg-primary text-background'
                            : 'hover:bg-accent',
                    ]"
                    title="Bullet List"
                >
                    <i class="ri-list-unordered" />
                </button>
            </li>
            <!--    ORDERED_LIST        -->
            <li>
                <button
                    @click="() => editor.chain().focus().toggleOrderedList().run()"
                    type="button"
                    class="cursor-pointer px-3 py-2"
                    :class="[
                        editor.isActive('orderedList')
                            ? 'bg-primary text-background'
                            : 'hover:bg-accent',
                    ]"
                    title="Bullet List"
                >
                    <i class="ri-list-ordered-2" />
                </button>
            </li>
            <!--    HEADING-2        -->
            <li>
                <button
                    @click="() => editor.chain().focus().toggleHeading({ level: 2 }).run()"
                    type="button"
                    class="px-3 py-2"
                    :class="[
                        editor.isActive('heading', { level: 2 })
                            ? 'bg-indigo-500 text-white'
                            : 'hover:bg-gray-200',
                    ]"
                    title="Heading 1"
                >
                    <i class="ri-h-1" />
                </button>
            </li>
            <!--    HEADING-3        -->
            <li>
                <button
                    @click="() => editor.chain().focus().toggleHeading({ level: 3 }).run()"
                    type="button"
                    class="px-3 py-2"
                    :class="[
                        editor.isActive('heading', { level: 3 })
                            ? 'bg-indigo-500 text-white'
                            : 'hover:bg-gray-200',
                    ]"
                    title="Heading 2"
                >
                    <i class="ri-h-2" />
                </button>
            </li>
            <!--    HEADING-4        -->
            <li>
                <button
                    @click="() => editor.chain().focus().toggleHeading({ level: 4 }).run()"
                    type="button"
                    class="px-3 py-2"
                    :class="[
                        editor.isActive('heading', { level: 4 })
                            ? 'bg-indigo-500 text-white'
                            : 'hover:bg-gray-200',
                    ]"
                    title="Heading 3"
                >
                    <i class="ri-h-3" />
                </button>
            </li>
            <!--    CENTERED TEXT        -->
            <li>
                <button
                    @click="toggleCenter"
                    type="button"
                    class="px-3 py-2"
                    :class="[
                        editor.isActive({ textAlign: 'center' })
                            ? 'bg-indigo-500 text-white'
                            : 'hover:bg-gray-200',
                    ]"
                    title="Text Align Center"
                >
                    <i class="ri-align-center" />
                </button>
            </li>
            <!--    ADD IMAGE        -->
            <li>
                <button
                    @click="openImgModal = true"
                    type="button"
                    class="px-3 py-2 hover:bg-gray-200"
                    title="Add link"
                >
                    <i class="ri-file-image-line" />
                </button>
            </li>
            <!--    ADD ANCHOR LINK        -->
            <li>
                <button
                    @click="addAnchorLink"
                    type="button"
                    class="px-3 py-2"
                    :class="[
                        editor.isActive('link') ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200',
                    ]"
                    title="Add link"
                >
                    <i class="ri-link" />
                </button>
            </li>
            <!--    ADD YOUTUBE        -->
            <li>
                <button
                    @click="addYoutube"
                    type="button"
                    class="px-3 py-2 hover:bg-gray-200"
                    title="Add link"
                >
                    <i class="ri-youtube-fill" />
                </button>
            </li>
        </menu>
        <EditorContent :editor="editor" class="flex-1 overflow-y-auto [&>.tiptap]:h-full" />

        <ImageModal v-model:open="openImgModal" @add-image="addImage"/>
    </div>
</template>
