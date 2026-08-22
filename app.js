document.documentElement.classList.add('js');

document.addEventListener('DOMContentLoaded', () => {
  document.body.classList.add('page-ready');

  document.querySelectorAll('.site-header').forEach((header) => {
    const nav = header.querySelector('.nav');
    if (!nav) return;
    const isAdminHeader = header.querySelector('.brand')?.textContent.includes('Admin');
    if (isAdminHeader) {
      const adminMenu = [
        ['index.php', 'Dashboard'],
        ['products.php', 'Products'],
        ['product-form.php', 'Add product'],
        ['orders.php', 'Orders'],
        ['../admin-logout.php', 'Logout'],
      ];
      const currentPage = window.location.pathname.split('/').pop() || 'index.php';
      const links = new Map([...nav.querySelectorAll('a')].map((link) => [link.getAttribute('href')?.split('?')[0], link]));
      adminMenu.forEach(([href, label]) => {
        let link = links.get(href);
        if (!link) {
          link = document.createElement('a');
          link.href = href;
          link.textContent = label;
        }
        link.classList.toggle('active-menu', href.split('/').pop() === currentPage);
        nav.appendChild(link);
      });
    } else {
      const customerMenu = [
        ['index.php', 'Home'],
        ['products.php', 'Shop'],
        ['cart.php', 'Cart'],
        ['orders.php', 'My Orders'],
        ['wishlist.php', 'Wishlist'],
        ['profile.php', 'Profile'],
        ['login.php', 'Login'],
        ['register.php', 'Register'],
        ['logout.php', 'Logout'],
      ];
      const currentPage = window.location.pathname.split('/').pop() || 'index.php';
      const links = new Map([...nav.querySelectorAll('a')].map((link) => [link.getAttribute('href')?.split('?')[0], link]));
      customerMenu.forEach(([href, label]) => {
        let link = links.get(href);
        if (!link) {
          link = document.createElement('a');
          link.href = href;
          link.textContent = label;
        }
        link.classList.toggle('my-orders-menu', label === 'My Orders');
        link.classList.toggle('active-menu', href === currentPage);
        nav.appendChild(link);
      });
    }
    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'menu-toggle';
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Open menu');
    toggle.innerHTML = '<span></span><span></span><span></span>';
    header.insertBefore(toggle, nav);
    toggle.addEventListener('click', () => {
      const isOpen = header.classList.toggle('menu-open');
      toggle.setAttribute('aria-expanded', String(isOpen));
      toggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
    });
  });

  document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', () => {
      const submitButton = form.querySelector('button[type="submit"], button:not([type])');
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.dataset.originalText = submitButton.textContent;
        submitButton.textContent = 'Please wait...';
      }
    });
  });

  document.querySelectorAll('input[name="mobile"]').forEach((input) => {
    input.addEventListener('input', () => {
      input.value = input.value.replace(/\D/g, '').slice(0, 12);
    });
  });

  const otpInput = document.querySelector('input[name="otp"]');
  if (otpInput) {
    otpInput.focus();
    otpInput.addEventListener('input', () => {
      otpInput.value = otpInput.value.replace(/\D/g, '').slice(0, 6);
    });
  }

  document.querySelectorAll('input[type="password"]').forEach((input) => {
    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'password-toggle';
    toggle.textContent = 'Show';
    toggle.addEventListener('click', () => {
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      toggle.textContent = isHidden ? 'Hide' : 'Show';
    });
    input.insertAdjacentElement('afterend', toggle);
  });
});
