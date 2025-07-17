<script setup lang="ts">
import { StarterKit } from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { Markdown } from 'tiptap-markdown';
import { watch } from 'vue';
import 'remixicon/fonts/remixicon.css';

const model = defineModel();

const editor = useEditor({
    extensions: [
        StarterKit.configure({
            code: false,
            codeBlock: false,
            heading: {
                levels: [2, 3, 4],
            },
        }),
        Markdown,
    ],
    editorProps: {
        attributes: {
            class: `min-h-[512px] prose prose-sm max-w-none py-1.5 px-3`,
        },
    },
    onUpdate: () => {
        model.value = editor.value?.storage.markdown.getMarkdown();
    },
});

const promptUserForHref = () => {
    if (editor.value?.isActive('link')) {
        return editor.value?.chain().unsetLink().run();
    }

    const href = prompt('Type in proper web address');

    if (!href) {
        return editor.value?.chain().focus().run();
    }

    return editor.value?.chain().focus().setLink({ href }).run();
};

watch(
    model,
    (value: any) => {
        if (value === editor.value?.storage.markdown.getMarkdown()) {
            return;
        }

        editor.value?.commands.setContent(value);
    },
    { immediate: true },
);
</script>

<template>
    <div
        v-if="editor"
        class="rounded-md border-0 bg-white shadow-sm ring-1 ring-gray-300 ring-inset focus:ring-[3px] focus:ring-indigo-600 focus:ring-inset focus-visible:ring-[3px]"
    >
        <menu class="flex divide-x border-b">
            <li>
                <button
                    @click="() => editor.chain().focus().toggleBold().run()"
                    type="button"
                    class="cursor-pointer rounded-tl-md px-3 py-2"
                    :class="[editor.isActive('bold') ? 'bg-primary text-background' : 'hover:bg-accent']"
                    title="Bold"
                >
                    <i class="ri-bold" />
                </button>
            </li>
            <li>
                <button
                    @click="() => editor.chain().focus().toggleItalic().run()"
                    type="button"
                    class="cursor-pointer px-3 py-2"
                    :class="[editor.isActive('italic') ? 'bg-primary text-background' : 'hover:bg-accent']"
                    title="Italic"
                >
                    <i class="ri-italic" />
                </button>
            </li>
            <li>
                <button
                    @click="() => editor.chain().focus().toggleStrike().run()"
                    type="button"
                    class="cursor-pointer px-3 py-2"
                    :class="[editor.isActive('strike') ? 'bg-primary text-background' : 'hover:bg-accent']"
                    title="Italic"
                >
                    <i class="ri-strikethrough-2" />
                </button>
            </li>
            <li>
                <button
                    @click="() => editor.chain().focus().toggleBlockquote().run()"
                    type="button"
                    class="cursor-pointer px-3 py-2"
                    :class="[editor.isActive('blockquote') ? 'bg-primary text-background' : 'hover:bg-accent']"
                    title="Blockquote"
                >
                    <i class="ri-double-quotes-l" />
                </button>
            </li>
            <li>
                <button
                    @click="() => editor.chain().focus().toggleBulletList().run()"
                    type="button"
                    class="cursor-pointer px-3 py-2"
                    :class="[editor.isActive('bulletList') ? 'bg-primary text-background' : 'hover:bg-accent']"
                    title="Bullet List"
                >
                    <i class="ri-list-unordered" />
                </button>
            </li>
            <li>
                <button
                    @click="() => editor.chain().focus().toggleOrderedList().run()"
                    type="button"
                    class="cursor-pointer px-3 py-2"
                    :class="[editor.isActive('orderedList') ? 'bg-primary text-background' : 'hover:bg-accent']"
                    title="Bullet List"
                >
                    <i class="ri-list-ordered-2" />
                </button>
            </li>
            <li>
                <button
                    @click="() => editor.chain().focus().toggleHeading({ level: 2 }).run()"
                    type="button"
                    class="px-3 py-2"
                    :class="[editor.isActive('heading', { level: 2 }) ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Heading 1"
                >
                    <i class="ri-h-1" />
                </button>
            </li>
            <li>
                <button
                    @click="() => editor.chain().focus().toggleHeading({ level: 3 }).run()"
                    type="button"
                    class="px-3 py-2"
                    :class="[editor.isActive('heading', { level: 3 }) ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Heading 2"
                >
                    <i class="ri-h-2" />
                </button>
            </li>
            <li>
                <button
                    @click="() => editor.chain().focus().toggleHeading({ level: 4 }).run()"
                    type="button"
                    class="px-3 py-2"
                    :class="[editor.isActive('heading', { level: 4 }) ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Heading 3"
                >
                    <i class="ri-h-3" />
                </button>
            </li>
            <li>
                <button
                    @click="promptUserForHref"
                    type="button"
                    class="px-3 py-2"
                    :class="[editor.isActive('link') ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200']"
                    title="Add link"
                >
                    <i class="ri-link" />
                </button>
            </li>
        </menu>
        <EditorContent :editor="editor" />
    </div>
</template>
