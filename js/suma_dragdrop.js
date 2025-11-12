document.addEventListener('DOMContentLoaded', () => {
  const palette = document.getElementById('number-palette');
  const slots = Array.from(document.querySelectorAll('#board-wrap .slot'));
  const digits = Array.from(document.querySelectorAll('#number-palette .digit'));

  // We'll clone digits from palette when placing so the palette remains intact.
  // Use instance ids for placed items so removal/move targets the exact element.
  let dragSrcEl = null;

  // Palette source digits: set value in dataTransfer
  digits.forEach(img => {
    img.addEventListener('dragstart', ev => {
      const val = img.dataset.value || img.alt || '';
      ev.dataTransfer.setData('text/value', val);
      ev.dataTransfer.setData('text/instance', '');
      dragSrcEl = img;
      try { ev.dataTransfer.setDragImage(img, img.width/2, img.height/2); } catch (e){}
      img.classList.add('dragging');
    });

    img.addEventListener('dragend', () => {
      dragSrcEl && dragSrcEl.classList.remove('dragging');
      dragSrcEl = null;
    });
  });

  // Palette accepts drops to remove placed digits
  palette.addEventListener('dragover', ev => { ev.preventDefault(); palette.classList.add('drop-target'); });
  palette.addEventListener('dragleave', () => palette.classList.remove('drop-target'));
  palette.addEventListener('drop', ev => {
    ev.preventDefault();
    palette.classList.remove('drop-target');
    const instanceId = ev.dataTransfer.getData('text/instance');
    if (instanceId) {
      const placed = document.querySelector(`#board .digit[data-instance-id="${instanceId}"]`);
      if (placed) placed.remove();
    }
  });

  // Slots behaviour: accept drops, one per slot. When dropping, clone a new digit from the palette
  slots.forEach(slot => {
    slot.addEventListener('dragover', ev => { ev.preventDefault(); slot.classList.add('slot--over'); });
    slot.addEventListener('dragleave', () => slot.classList.remove('slot--over'));
    slot.addEventListener('drop', ev => {
      ev.preventDefault();
      slot.classList.remove('slot--over');
      const instanceId = ev.dataTransfer.getData('text/instance');
      const value = ev.dataTransfer.getData('text/value');

      // If moving an existing placed element
      if (instanceId) {
        const placed = document.querySelector(`#board .digit[data-instance-id="${instanceId}"]`);
        if (!placed) return;
        const existing = slot.querySelector('.digit[data-instance-id]');
        if (existing) existing.remove();
        slot.appendChild(placed);
        return;
      }

      // If dropping from the palette (value present) clone a new placed digit
      if (value) {
        const src = Array.from(palette.querySelectorAll('.digit')).find(d => (d.dataset.value||d.alt) == String(value));
        if (!src) return;

        const clone = src.cloneNode(true);
        const iid = 'i' + Date.now() + '-' + Math.floor(Math.random()*1000);
        clone.setAttribute('data-instance-id', iid);
        clone.setAttribute('data-placed', 'true');
        clone.draggable = true;

        // placed digits can be dragged (we set instance id on drag)
        clone.addEventListener('dragstart', e => {
          e.dataTransfer.setData('text/instance', iid);
          e.dataTransfer.setData('text/value', value);
          try { e.dataTransfer.setDragImage(clone, clone.width/2, clone.height/2); } catch (err){}
        });

        // clicking placed digit removes it
        clone.addEventListener('click', () => clone.remove());

        const existing = slot.querySelector('.digit[data-instance-id]');
        if (existing) existing.remove();
        slot.appendChild(clone);
      }
    });
  });
});
