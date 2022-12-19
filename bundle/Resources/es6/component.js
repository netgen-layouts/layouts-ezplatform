const handlePostRequest = async (id) => {
    const data = localStorage.getItem(id);
    if (!data) return;

    const { blockId, contentId, locale } = JSON.parse(data);

    const nglayoutsBasePathElement = document.querySelector('[name="nglayouts-base-path"]');
    const nglayoutsBasePath = nglayoutsBasePathElement && nglayoutsBasePathElement.getAttribute('content');

    const url = `${nglayoutsBasePath}ezadmin/blocks/${blockId}/${locale}/connect-component-content/${contentId}`;
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
    const contentId = document.querySelector('[name="nglayouts-content-id"]').getAttribute('content');

    const data = { blockId, contentId, locale };
    localStorage.setItem(contentId, JSON.stringify(data));
};

const connectBlockAndContent = async () => {
    const urlPathname = window.location.pathname;
    const urlHash = window.location.hash;

    const isDraftElement = document.querySelector('[name="nglayouts-is-new-draft"]');
    const isDraftAttribute = isDraftElement && isDraftElement.getAttribute('content');
    const isNewDraft = (isDraftAttribute && isDraftAttribute === 'true');

    if (isNewDraft) {
        saveDataToLocalStorage(urlPathname, urlHash);
    } else {
        const contentIdElement = document.querySelector('[name="nglayouts-content-id"]');
        const contentId = contentIdElement && contentIdElement.getAttribute('content');

        if (!contentId) return;

        handlePostRequest(contentId);
    }
};

window.addEventListener('DOMContentLoaded', () => {
    connectBlockAndContent();
});
