function filterInput()
{
    const items = document.querySelectorAll('.roleListItem')
    const filter = document.querySelector('#inputFilter').value
    const regex = new RegExp(filter, 'i')
    const isFoundInText = item => regex.test(item.innerText)
    const setStyleDisplay = child => {
        child.style.display = isFoundInText(child) ? '' : 'none'
    }

    items.forEach(setStyleDisplay)
}
