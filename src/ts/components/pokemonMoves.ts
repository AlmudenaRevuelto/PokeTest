export function initPokemonMoves() {
    const list = document.getElementById('pokemon-moves-list');
    const addBtn = document.querySelector('.add-move');

    if (!list || !addBtn) return;

    addBtn.addEventListener('click', () => {
        const index = list.children.length;

        const li = document.createElement('li');

        li.innerHTML = `
            <input type="text" name="pokemon_moves[${index}][name]" placeholder="Move Name" />
            <textarea name="pokemon_moves[${index}][description]" placeholder="Move Description"></textarea>
            <button type="button" class="button remove-move">Remove</button>
        `;

        list.appendChild(li);
    });

    list.addEventListener('click', (e) => {
        const target = e.target as HTMLElement;

        if (target.classList.contains('remove-move')) {
            target.closest('li')?.remove();
        }
    });
}