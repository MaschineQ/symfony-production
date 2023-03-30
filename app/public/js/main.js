/*
    collection add / remove
 */

const addItemLink = document.createElement('a')
addItemLink.classList.add('add_item', 'inline-block', 'bg-green-500', 'hover:bg-green-700', 'text-white', 'font-bold', 'p-2', 'rounded', 'cursor-pointer', 'my-2');
addItemLink.href='#'
addItemLink.innerHTML = 'Add item';
addItemLink.dataset.collectionHolderClass='items'

const addItemFormDeleteLink = (item) => {
    const removeLink = document.createElement('a');
    removeLink.href = '#';
    removeLink.classList.add('remove', 'fas', 'fa-trash', 'text-2l', 'text-red-500', 'ml-2', 'cursor-pointer');

    item.appendChild(removeLink);

    removeLink.addEventListener('click', (e) => {
        e.preventDefault();
        item.remove();
    })
}

const newLinkLi = document.createElement('li').append(addItemLink)

const collectionHolder = document.querySelector('ul.items')
collectionHolder.appendChild(addItemLink)

const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('li');

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );

    collectionHolder.appendChild(item);
    collectionHolder.dataset.index++;

    // add a delete link to the new form
    addItemFormDeleteLink(item);
}

addItemLink.addEventListener("click", addFormToCollection)

document
    .querySelectorAll('ul.items li')
    .forEach((tag) => {
        addItemFormDeleteLink(tag)
    })

// show first item
if (collectionHolder.dataset.index === '0') {
    addItemLink.click()
}