const navLinks = document.querySelectorAll('.nav-link[data-section]');
const dotEls = document.querySelectorAll('.carousel-dot[data-dot]');
const mobileNav = document.querySelector('#mobileNav');
const navToggleBtn = document.querySelector('[data-nav-toggle]');
const heroContent = document.querySelector('#heroContent');
const eyebrowEl = document.querySelector('#heroEyebrow');
const titleEl = document.querySelector('#heroTitle');
const subtitleEl = document.querySelector('#heroSubtitle');
const descriptionEl = document.querySelector('#heroDescription');
const ctaEl = document.querySelector('#heroCta');

const contentMap = {
  home: {
    eyebrow: 'Aprende',
    title: 'MATEMÁTICAS',
    subtitle: 'Ejercicios interactivos',
    description:
      'Potencia el pensamiento lógico con retos diarios, material manipulativo y actividades guiadas paso a paso pensadas para primaria.',
    ctaLabel: 'Comenzar ahora',
    ctaHref: '#learn-more'
  },
  pricing: {
    eyebrow: 'Planes',
    title: 'PRECIOS FLEXIBLES',
    subtitle: 'Ajustados a tu aula',
    description:
      'Elige planes mensuales, trimestrales o anuales con acceso a tutorías, reportes de progreso y material descargable para toda la clase.',
    ctaLabel: 'Ver planes',
    ctaHref: '#pricing'
  },
  services: {
    eyebrow: 'Servicios',
    title: 'ACOMPAÑAMIENTO EXPERTO',
    subtitle: 'Mentores certificados',
    description:
      'Mentores certificados acompañan cada lección con sesiones en vivo, correcciones personalizadas y comunicación directa con familias.',
    ctaLabel: 'Ver servicios',
    ctaHref: '#services'
  }
};

let activeSection = 'home';
let isAnimating = false;

const setActiveNav = section => {
  navLinks.forEach(link => {
    const isActive = link.dataset.section === section;
    link.classList.toggle('nav-link--active', isActive);
    link.setAttribute('aria-pressed', String(isActive));
  });
};

const setActiveDot = section => {
  dotEls.forEach(dot => {
    const isActive = dot.dataset.dot === section;
    dot.classList.toggle('carousel-dot--active', isActive);
    dot.setAttribute('aria-selected', String(isActive));
  });
};

const updateHeroContent = section => {
  const content = contentMap[section] ?? contentMap.home;
  eyebrowEl.textContent = content.eyebrow;
  titleEl.textContent = content.title;
  subtitleEl.textContent = content.subtitle;
  descriptionEl.textContent = content.description;
  ctaEl.textContent = content.ctaLabel;
  ctaEl.setAttribute('href', content.ctaHref);
};

const animateContentChange = section => {
  if (!heroContent) return;

  heroContent.classList.remove('animate-content-in');
  heroContent.classList.add('animate-content-out');

  const handleOut = () => {
    heroContent.classList.remove('animate-content-out');
    updateHeroContent(section);
    heroContent.classList.add('animate-content-in');

    heroContent.addEventListener(
      'animationend',
      () => {
        heroContent.classList.remove('animate-content-in');
        isAnimating = false;
      },
      { once: true }
    );
  };

  heroContent.addEventListener('animationend', handleOut, { once: true });
};

const closeMobileNav = () => {
  if (!mobileNav || !navToggleBtn) return;
  mobileNav.classList.add('hidden');
  navToggleBtn.setAttribute('aria-expanded', 'false');
};

const toggleMobileNav = () => {
  if (!mobileNav || !navToggleBtn) return;
  const isHidden = mobileNav.classList.contains('hidden');
  mobileNav.classList.toggle('hidden', !isHidden);
  navToggleBtn.setAttribute('aria-expanded', String(isHidden));
};

const handleSectionChange = section => {
  if (section === activeSection || isAnimating) return;

  activeSection = section;
  isAnimating = true;
  setActiveNav(section);
  setActiveDot(section);
  animateContentChange(section);
  closeMobileNav();
};

const init = () => {
  if (!heroContent) return;

  updateHeroContent(activeSection);
  setActiveNav(activeSection);
  setActiveDot(activeSection);

  navLinks.forEach(link => {
    link.addEventListener('click', event => {
      event.preventDefault();
      const { section } = link.dataset;
      if (!section) return;
      handleSectionChange(section);
    });
  });

  dotEls.forEach(dot => {
    dot.addEventListener('click', event => {
      event.preventDefault();
      const { dot: section } = dot.dataset;
      if (!section) return;
      handleSectionChange(section);
    });

    dot.addEventListener('keydown', event => {
      if (event.key !== 'Enter' && event.key !== ' ') return;
      event.preventDefault();
      const { dot: section } = dot.dataset;
      if (!section) return;
      handleSectionChange(section);
    });
  });

  navToggleBtn?.addEventListener('click', () => {
    toggleMobileNav();
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
      closeMobileNav();
    }
  });

  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
      closeMobileNav();
    }
  });
};

init();
