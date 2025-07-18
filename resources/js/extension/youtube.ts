// resources/js/Extensions/Youtube.ts
import { Node } from '@tiptap/core';

export default Node.create({
    name: 'youtube',

    group: 'block',
    atom: true,
    selectable: true,

    addAttributes() {
        return {
            videoId: {
                default: null,
            },
        };
    },

    parseHTML() {
        return [
            {
                tag: 'iframe[src*="youtube.com"]',
                getAttrs: (node) => {
                    const src = (node as HTMLIFrameElement).getAttribute('src');
                    const match = src?.match(/youtube\.com\/embed\/([\w-]+)/);
                    const videoId = match?.[1];
                    return videoId ? { videoId } : false;
                },
            },
        ];
    },

    renderHTML({ HTMLAttributes }) {
        return [
            'div',
            {
                class: 'youtube-wrapper',
            },
            [
                'iframe',
                {
                    src: `https://www.youtube.com/embed/${HTMLAttributes.videoId}`,
                    frameborder: '0',
                    allowfullscreen: 'true',
                },
            ],
        ];
    },

    addNodeView() {
        return ({ node }) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'youtube-wrapper';

            const iframe = document.createElement('iframe');
            iframe.setAttribute('src', `https://www.youtube.com/embed/${node.attrs.videoId}`);
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('allowfullscreen', 'true');

            wrapper.appendChild(iframe);

            return {
                dom: wrapper,
            };
        };
    },
});
