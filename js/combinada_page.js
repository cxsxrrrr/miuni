(function(){
  const toastEl = document.getElementById('toast');
  const checkBtn = document.getElementById('checkBtn');
  const skipBtn = document.getElementById('skipBtn');
  const resetBtn = document.getElementById('resetSlots');
  const palette = document.getElementById('number-palette');
  const exercise = window.currentExercise || null;
  const getHelpers = () => window.combinadaBoardHelpers || null;
  const messages = window.combinadaMessages || {};

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

  const normalizeNumericString = (value) => {
    if (value === null || value === undefined) {
      return null;
    }
    const sanitized = String(value).replace(/[^0-9]/g, '');
    if (!sanitized.length) {
      return null;
    }
    const trimmed = sanitized.replace(/^0+/, '');
    return trimmed === '' ? '0' : trimmed;
  };

  const targetValue = normalizeNumericString(exercise.result);

  const syncNavigationLock = () => {
    if (!skipBtn) return;
    const locked = exercise.status === 'incorrect';
    skipBtn.disabled = locked;
    skipBtn.classList.toggle('opacity-60', locked);
    skipBtn.classList.toggle('cursor-not-allowed', locked);
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
        syncNavigationLock();
      }
      return data;
    } catch (error) {
      console.error(error);
      showToast('No pudimos guardar tu progreso. Intenta de nuevo.', 'info');
      return null;
    }
  };

  const successMessage = messages.success || '¡Excelente trabajo!';

  const checkAnswer = async () => {
    const digits = slotOrder.map(slotId => getSlotDigit(slotId));
    const firstFilledIndex = digits.findIndex(digit => digit !== null);

    if (firstFilledIndex === -1) {
      showToast('Completa la respuesta antes de verificar.', 'info');
      await markResult('pending');
      return;
    }

    const relevantDigits = digits.slice(firstFilledIndex);
    if (relevantDigits.some(digit => digit === null)) {
      showToast('Completa la respuesta antes de verificar.', 'info');
      await markResult('pending');
      return;
    }

    const rawAnswer = relevantDigits.join('');
    const normalizedAnswer = normalizeNumericString(rawAnswer);

    if (normalizedAnswer === null || targetValue === null) {
      showToast('Completa la respuesta antes de verificar.', 'info');
      await markResult('pending');
      return;
    }

    if (normalizedAnswer === targetValue) {
      showToast(successMessage, 'success');
      checkBtn?.setAttribute('disabled', 'true');
      await markResult('correct', rawAnswer);
      return;
    }

    showToast('Revisa tu resultado y vuelve a intentarlo.', 'error');
    await markResult('incorrect', rawAnswer);
  };

  checkBtn?.addEventListener('click', checkAnswer);

  skipBtn?.addEventListener('click', () => {
    if (exercise.status === 'correct') {
      window.location.href = 'combinadas.php';
      return;
    }

    if (exercise.status === 'incorrect') {
      showToast('No puedes volver hasta corregir la respuesta o vaciarla.', 'error');
      return;
    }

    exercise.status = 'pending';
    syncNavigationLock();
    markResult('pending').finally(() => {
      window.location.href = 'combinadas.php';
    });
  });

  resetBtn?.addEventListener('click', () => {
    clearSlots();
    checkBtn?.removeAttribute('disabled');
    exercise.status = 'pending';
    syncNavigationLock();
    showToast('La respuesta se limpió. ¡Intenta de nuevo!', 'info');
    markResult('pending');
  });

  if (exercise.status === 'correct') {
    checkBtn?.setAttribute('disabled', 'true');
  }

  syncNavigationLock();

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


