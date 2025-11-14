document.addEventListener('DOMContentLoaded', () => {
  const palette = document.getElementById('number-palette');
  const board = document.getElementById('board');
  const slots = Array.from(document.querySelectorAll('#board-wrap .slot[data-slot^="b"]'));
  const digits = Array.from(document.querySelectorAll('#number-palette .digit'));

  if (!palette || !board) {
    return;
  }

  const soundSources = {
    drag: 'assets/audio/drag.mp3',
    drop: 'assets/audio/drop.mp3'
  };

  const audioCache = {};

  const playSound = name => {
    const src = soundSources[name];
    if (!src) return;
    let audio = audioCache[name];
    if (!audio) {
      audio = new Audio(src);
      audio.preload = 'auto';
      audioCache[name] = audio;
    }
    try {
      if (!audio.paused) {
        audio.pause();
      }
      audio.currentTime = 0;
      audio.play().catch(() => {});
    } catch (err) {
      console.warn('No fue posible reproducir el sonido', err);
    }
  };

  const makePlacedDigit = value => {
    const val = String(value);
    const src = Array.from(palette.querySelectorAll('.digit')).find(d => (d.dataset.value || d.alt || '') === val);
    if (!src) return null;

    const clone = src.cloneNode(true);
    const iid = 'i' + Date.now() + '-' + Math.floor(Math.random() * 1000);
    clone.setAttribute('data-instance-id', iid);
    clone.setAttribute('data-placed', 'true');
    clone.draggable = true;

    clone.addEventListener('dragstart', e => {
      e.dataTransfer.setData('text/instance', iid);
      e.dataTransfer.setData('text/value', val);
      clone.classList.add('dragging');
      playSound('drag');
      try { e.dataTransfer.setDragImage(clone, clone.width / 2, clone.height / 2); } catch (err) {}
    });

    clone.addEventListener('dragend', () => {
      clone.classList.remove('dragging');
    });

    clone.addEventListener('click', () => clone.remove());

    return clone;
  };

  const clearSlot = slotId => {
    const slot = document.querySelector(`.slot[data-slot="${slotId}"]`);
    if (slot) {
      slot.querySelectorAll('.digit[data-instance-id]').forEach(node => node.remove());
    }
  };

  const placeDigit = (slotId, value) => {
    const slot = document.querySelector(`.slot[data-slot="${slotId}"]`);
    if (!slot) return false;
    const clone = makePlacedDigit(value);
    if (!clone) return false;
    const existing = slot.querySelector('.digit[data-instance-id]');
    if (existing) existing.remove();
    slot.appendChild(clone);
    return true;
  };

  const clearAllSlots = () => {
    slots.forEach(slot => slot.querySelectorAll('.digit[data-instance-id]').forEach(node => node.remove()));
  };

  let dragSrcEl = null;

  digits.forEach(img => {
    img.addEventListener('dragstart', ev => {
      const val = img.dataset.value || img.alt || '';
      ev.dataTransfer.setData('text/value', val);
      ev.dataTransfer.setData('text/instance', '');
      dragSrcEl = img;
      try { ev.dataTransfer.setDragImage(img, img.width / 2, img.height / 2); } catch (e) {}
      img.classList.add('dragging');
      playSound('drag');
    });

    img.addEventListener('dragend', () => {
      dragSrcEl && dragSrcEl.classList.remove('dragging');
      dragSrcEl = null;
    });
  });

  palette.addEventListener('dragover', ev => { ev.preventDefault(); palette.classList.add('drop-target'); });
  palette.addEventListener('dragleave', () => palette.classList.remove('drop-target'));
  palette.addEventListener('drop', ev => {
    ev.preventDefault();
    palette.classList.remove('drop-target');
    const instanceId = ev.dataTransfer.getData('text/instance');
    if (instanceId) {
      const placed = board.querySelector(`.digit[data-instance-id="${instanceId}"]`);
      if (placed) {
        placed.remove();
        playSound('drop');
      }
    }
  });

  slots.forEach(slot => {
    slot.addEventListener('dragover', ev => { ev.preventDefault(); slot.classList.add('slot--over'); });
    slot.addEventListener('dragleave', () => slot.classList.remove('slot--over'));
    slot.addEventListener('drop', ev => {
      ev.preventDefault();
      slot.classList.remove('slot--over');
      const instanceId = ev.dataTransfer.getData('text/instance');
      const value = ev.dataTransfer.getData('text/value');

      if (instanceId) {
        const placed = board.querySelector(`.digit[data-instance-id="${instanceId}"]`);
        if (!placed) return;
        const existing = slot.querySelector('.digit[data-instance-id]');
        if (existing) existing.remove();
        slot.appendChild(placed);
        playSound('drop');
        return;
      }

      if (value) {
        if (placeDigit(slot.dataset.slot, value)) {
          playSound('drop');
        }
      }
    });
  });

  window.combinadaBoardHelpers = {
    placeDigit,
    clearSlot,
    clearAllSlots
  };
});
