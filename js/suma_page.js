(function(){
  const toastEl = document.getElementById('toast');
  const checkBtn = document.getElementById('checkBtn');
  const skipBtn = document.getElementById('skipBtn');
  const resetBtn = document.getElementById('resetSlots');
  const palette = document.getElementById('number-palette');
  const exercise = window.currentExercise || null;
  const getHelpers = () => window.sumaBoardHelpers || null;

  if (!exercise) {
    console.warn('No exercise payload found');
    return;
  }

  const slotOrder = exercise.slots?.answer || ['b1', 'b2', 'b3', 'b4', 'b5', 'b6'];

  const showToast = (message, tone) => {
    if (!toastEl) return;
    toastEl.innerHTML = `
      <div class="flex items-start gap-3 p-4 rounded-xl shadow-lg text-sm ${tone === 'success' ? 'bg-emerald-50 text-emerald-700' : tone === 'error' ? 'bg-rose-50 text-rose-700' : 'bg-white text-slate-700'}">
        <span class="text-lg">${tone === 'success' ? '✔' : tone === 'error' ? '⚠' : 'ℹ'}</span>
        <div class="flex-1">${message}</div>
        <button aria-label="Cerrar" class="ml-2 text-sm" data-toast-close>×</button>
      </div>`;
    toastEl.classList.remove('hidden');
    const closeBtn = toastEl.querySelector('[data-toast-close]');
    closeBtn?.addEventListener('click', () => toastEl.classList.add('hidden'));
    setTimeout(() => toastEl.classList.add('hidden'), 4000);
  };

  const getSlotDigit = slotId => {
    const slot = document.querySelector(`.slot[data-slot="${slotId}"]`);
    if (!slot) return null;
    const placed = slot.querySelector('.digit[data-instance-id]');
    if (!placed) return null;
    const value = placed.dataset.value || placed.alt || '';
    return value.trim() === '' ? null : value.trim();
  };

  const clearSlots = () => {
    const helpers = getHelpers();
    if (helpers?.clearAllSlots) {
      helpers.clearAllSlots();
      return;
    }
    slotOrder.forEach(slotId => {
      const slot = document.querySelector(`.slot[data-slot="${slotId}"]`);
      slot?.querySelectorAll('.digit[data-instance-id]').forEach(node => node.remove());
    });
  };

  const collectAnswer = () => {
    const digits = slotOrder.map(slotId => getSlotDigit(slotId) ?? '');
    const joined = digits.join('');
    return joined.length ? joined : null;
  };

  const markResult = async (status, answer = null) => {
    try {
      const payload = { exerciseId: exercise.id, status };
      if (typeof answer === 'string') {
        payload.answer = answer;
      }
      const response = await fetch('services/resultado.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      if (!response.ok) {
        throw new Error('Respuesta no válida del servidor');
      }
      const data = await response.json();
      if (data?.completed !== undefined) {
        const progress = document.getElementById('progress-count');
        if (progress) progress.textContent = data.completed;
      }
      if (data?.status) {
        exercise.status = data.status;
      }
      return data;
    } catch (error) {
      console.error(error);
      showToast('No pudimos guardar tu progreso. Intenta de nuevo.', 'info');
      return null;
    }
  };

  const expectedDigits = (() => {
    const raw = String(exercise.sum);
    const padding = Math.max(0, slotOrder.length - raw.length);
    return Array(padding).fill(null).concat(raw.split(''));
  })();

  // Nueva función para actualizar el estado del botón skip
  function updateSkipBtn() {
    if (exercise.status === 'incorrect') {
      skipBtn?.setAttribute('disabled', 'true');
      skipBtn?.style.setProperty('opacity', '.5');
      skipBtn?.style.setProperty('pointer-events', 'none');
    } else {
      skipBtn?.removeAttribute('disabled');
      skipBtn?.style.removeProperty('opacity');
      skipBtn?.style.removeProperty('pointer-events');
    }
  }

  // Modifica checkAnswer para actualizar el botón después de verificar
  const checkAnswer = async () => {
    let allCorrect = true;

    slotOrder.forEach((slotId, index) => {
      const expected = expectedDigits[index];
      const value = getSlotDigit(slotId);

      if (expected === null) {
        if (value !== null) {
          allCorrect = false;
        }
        return;
      }

      if (value === null || value !== expected) {
        allCorrect = false;
      }
    });

    const userAnswer = collectAnswer();

    if (allCorrect) {
      showToast('¡Excelente! Has resuelto la suma correctamente.', 'success');
      checkBtn?.setAttribute('disabled', 'true');
      await markResult('correct', userAnswer);
      exercise.status = 'correct'; // Actualiza el estado local
    } else {
      showToast('Revisa tu resultado y vuelve a intentarlo.', 'error');
      await markResult('incorrect', userAnswer);
      exercise.status = 'incorrect'; // Actualiza el estado local
    }
    updateSkipBtn(); // <-- Actualiza el botón después de verificar
  };

  checkBtn?.addEventListener('click', checkAnswer);

  skipBtn?.addEventListener('click', () => {
    // Si el botón está deshabilitado, muestra el toast y no hace nada
    if (skipBtn.hasAttribute('disabled')) {
      showToast('Debes responder correctamente o vaciar la respuesta para salir.', 'error');
      return;
    }
    if (exercise.status && exercise.status !== 'pending') {
      window.location.href = 'sumas.php';
      return;
    }
    markResult('pending').finally(() => {
      window.location.href = 'sumas.php';
    });
  });

  resetBtn?.addEventListener('click', async () => {
    clearSlots();
    checkBtn?.removeAttribute('disabled');
    await markResult('pending');
    exercise.status = 'pending'; // Actualiza el estado local
    updateSkipBtn(); // <-- Actualiza el botón después de vaciar
    showToast('La respuesta se limpió. ¡Intenta de nuevo!', 'info');
  });

  // Llama a la función al cargar la página
  updateSkipBtn();

  const prefillAnswer = () => {
    if (!exercise.answer) return true;
    const helpers = getHelpers();
    if (!helpers) return false;
    const raw = String(exercise.answer).replace(/[^0-9]/g, '');
    if (!raw.length) return true;
    const digits = raw.split('');
    const padding = Math.max(0, slotOrder.length - digits.length);
    const filled = Array(padding).fill(null).concat(digits);

    filled.forEach((digit, index) => {
      const slotId = slotOrder[index];
      if (!slotId) return;
      if (digit === null) {
        helpers.clearSlot?.(slotId);
        return;
      }
      helpers.placeDigit?.(slotId, digit);
    });

    return true;
  };

  const schedulePrefill = () => {
    const attempt = () => {
      if (!prefillAnswer()) {
        setTimeout(attempt, 50);
      }
    };

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
      setTimeout(attempt, 0);
    } else {
      document.addEventListener('DOMContentLoaded', attempt, { once: true });
    }
  };

  schedulePrefill();

  window.addEventListener('beforeunload', () => {
    palette?.classList.remove('drop-target');
  });
})();
