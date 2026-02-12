const main_skeleton = document.getElementById('main_skeleton');
const main_content = document.getElementById('main_content');

export function showPropertieSkeleton() {
    main_skeleton.style.display = 'flex';
    main_content.style.display = 'none';
}

export function hidePropertieSkeleton() {
    main_skeleton.style.display = 'none';
    main_content.style.display = 'flex';
}