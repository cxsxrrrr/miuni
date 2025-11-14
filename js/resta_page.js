(function(){
  const toastEl = document.getElementById('toast');
  const checkBtn = document.getElementById('checkBtn');
  const skipBtn = document.getElementById('skipBtn');
  const resetBtn = document.getElementById('resetSlots');
  const palette = document.getElementById('number-palette');
  const modalEl = document.getElementById('congratsModal');
  const boardScaler = document.querySelector('[data-board-scaler]');
  const boardEl = document.getElementById('board');
  const exercise = window.currentExercise || null;
  const getHelpers = () => window.restaBoardHelpers || null;

  const BOARD_BASE_WIDTH = 720;
  const BOARD_BASE_HEIGHT = 420;

  const setupBoardScaling = () => {
    if (!boardScaler || !boardEl) {
      return;
    }

    const applyScale = () => {
      const availableWidth = boardScaler.clientWidth;
      if (!availableWidth) {
        return;
      }
      const scale = Math.min(availableWidth / BOARD_BASE_WIDTH, 1);
      boardEl.style.transform = `scale(${scale})`;
      boardEl.style.transformOrigin = 'top left';
      boardScaler.style.height = `${(BOARD_BASE_HEIGHT * scale).toFixed(2)}px`;
      boardScaler.style.visibility = 'visible';
    };

    const scheduleApply = () => window.requestAnimationFrame(applyScale);

    scheduleApply();
    window.addEventListener('resize', scheduleApply);
    window.addEventListener('orientationchange', () => setTimeout(applyScale, 100));

    if (typeof ResizeObserver === 'function') {
      const observer = new ResizeObserver(scheduleApply);
      observer.observe(boardScaler);
      boardScaler.__boardResizeObserver = observer;
    }
  };

  setupBoardScaling();

  if (!exercise) {
    console.warn('No exercise payload found');
    return;
  }

  const slotOrder = exercise.slots?.answer || ['b1', 'b2', 'b3', 'b4', 'b5', 'b6'];
  let exitWarningShown = exercise.status === 'incorrect';
  let congratsShown = false;
  const toNumber = value => {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : 0;
  };
  exercise.completed = toNumber(exercise.completed);
  exercise.total = toNumber(exercise.total);

  const toastStyles = {
    success: { wrapper: 'bg-emerald-50 text-emerald-700', icon: '✔' },
    error: { wrapper: 'bg-rose-50 text-rose-700', icon: '⚠' },
    warning: { wrapper: 'bg-amber-50 text-amber-800', icon: '⚠' },
    info: { wrapper: 'bg-white/95 text-slate-700', icon: 'ℹ' }
  };

  const soundSources = {
    victory: 'assets/audio/victory.mp3',
    bad: 'assets/audio/bad.mp3'
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

  const showToast = (message, tone = 'info') => {
    if (!toastEl) return;
    const style = toastStyles[tone] || toastStyles.info;
    const duration = tone === 'warning' ? 6000 : 4000;
    toastEl.innerHTML = `
      <div class="flex items-start gap-3 p-4 rounded-xl shadow-lg text-sm ${style.wrapper}">
        <span class="text-lg">${style.icon}</span>
        <div class="flex-1">${message}</div>
        <button aria-label="Cerrar" class="ml-2 text-sm" data-toast-close>×</button>
      </div>`;
    toastEl.classList.remove('hidden');
    const closeBtn = toastEl.querySelector('[data-toast-close]');
    closeBtn?.addEventListener('click', () => toastEl.classList.add('hidden'));
    setTimeout(() => toastEl.classList.add('hidden'), duration);
  };

  const hideCongratsModal = () => {
    if (!modalEl) return;
    modalEl.classList.add('hidden');
    modalEl.classList.remove('flex');
  };

  const showCongratsModal = () => {
    if (!modalEl || congratsShown) return;
    congratsShown = true;
    modalEl.classList.remove('hidden');
    modalEl.classList.add('flex');
  };

  if (modalEl) {
    modalEl.querySelectorAll('[data-congrats-close]').forEach(btn => {
      btn.addEventListener('click', hideCongratsModal);
    });
    modalEl.addEventListener('click', event => {
      if (event.target === modalEl) {
        hideCongratsModal();
      }
    });
  }

  const maybeShowCongrats = () => {
    if (congratsShown) return;
    if (exercise.total === 8 && exercise.completed >= exercise.total) {
      showCongratsModal();
    }
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

  const targetValue = normalizeNumericString(exercise.difference);

  const syncNavigationLock = () => {
    if (!skipBtn) return;
    const locked = exercise.status === 'incorrect';
    skipBtn.disabled = false;
    skipBtn.classList.remove('opacity-60', 'cursor-not-allowed');
    skipBtn.classList.toggle('ring-2', locked);
    skipBtn.classList.toggle('ring-offset-2', locked);
    skipBtn.classList.toggle('ring-rose-400', locked);
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
        exercise.completed = toNumber(data.completed);
        maybeShowCongrats();
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

  const checkAnswer = async () => {
    const digits = slotOrder.map(slotId => getSlotDigit(slotId));
    const firstFilledIndex = digits.findIndex(digit => digit !== null);

    if (firstFilledIndex === -1) {
      showToast('Completa la respuesta antes de verificar.', 'info');
      await markResult('pending');
      return;
    }

    let lastFilledIndex = -1;
    for (let idx = digits.length - 1; idx >= 0; idx -= 1) {
      if (digits[idx] !== null) {
        lastFilledIndex = idx;
        break;
      }
    }

    if (lastFilledIndex < firstFilledIndex) {
      lastFilledIndex = firstFilledIndex;
    }

    const relevantDigits = digits.slice(firstFilledIndex, lastFilledIndex + 1);
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
      playSound('victory');
      showToast('¡Excelente! Has resuelto la resta correctamente.', 'success');
      checkBtn?.setAttribute('disabled', 'true');
      await markResult('correct', rawAnswer);
      return;
    }

    playSound('bad');
    if (!exitWarningShown) {
      exitWarningShown = true;
      showToast('Revisa tu resultado. Si sales ahora, no podras repetir este ejercicio.', 'warning');
    } else {
      showToast('Revisa tu resultado y vuelve a intentarlo.', 'error');
    }
    await markResult('incorrect', rawAnswer);
  };

  checkBtn?.addEventListener('click', checkAnswer);

  skipBtn?.addEventListener('click', () => {
    const redirect = () => { window.location.href = 'restas.php'; };
    if (exercise.status === 'pending') {
      markResult('pending').finally(redirect);
      return;
    }
    redirect();
  });

  resetBtn?.addEventListener('click', () => {
    clearSlots();
    checkBtn?.removeAttribute('disabled');
    exercise.status = 'pending';
    syncNavigationLock();
    showToast('La respuesta se limpio. ¡Intenta de nuevo!', 'info');
    markResult('pending');
  });

  if (exercise.status === 'correct') {
    checkBtn?.setAttribute('disabled', 'true');
  }

  syncNavigationLock();
  maybeShowCongrats();

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
