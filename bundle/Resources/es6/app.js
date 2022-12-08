import 'bootstrap/js/src/modal';
import './location';

import '../sass/ibexa/admin/style.scss';

const handlePostRequest = async (id) => {
    const data = localStorage.getItem(id);
    if (!data) return;

    const { blockId, contentId, locale } = JSON.parse(data);

    const nglayoutsBasePath = document.querySelector('[name="nglayouts-base-path"]').getAttribute('content');
    const url = `${nglayoutsBasePath}ibexa/admin/blocks/${blockId}/${locale}/connect-component-content/${contentId}`;
    const bc = new BroadcastChannel('publish_content');

    await fetch(url, { method: 'post' })
    .then(() => {
            bc.postMessage(JSON.parse(data));
        })
        .then(() => {
            bc.close();
            localStorage.removeItem(contentId);
        });
};

const saveDataToLocalStorage = (pathname, hash) => {
    if (!hash.includes('#ngl-component/')) return;

    const params = hash.replace('#ngl-component/', '').split('/');
    const blockId = params[0];
    const locale = params[1];
    const contentId = pathname.split('content/edit/draft/').splice(-1)[0].split('/')[0];

    const data = { blockId, contentId, locale };
    localStorage.setItem(contentId, JSON.stringify(data));
};

const connectBlockAndContent = async () => {
    const urlPathname = window.location.pathname;
    const urlHash = window.location.hash;

    if (urlPathname.includes('view/content/') && urlPathname.includes('full')) {
        const contentId = urlPathname.split('content/').splice(-1)[0].split('/')[0];

        handlePostRequest(contentId);
    } else {
        saveDataToLocalStorage(urlPathname, urlHash);
    }
};

window.addEventListener('DOMContentLoaded', () => {
    connectBlockAndContent();
});
