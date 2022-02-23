// Handler that uses various data-* attributes to trigger
// specific actions, mimicing bootstraps attributes
const btnTriggers = Array.from(document.querySelectorAll('.product-list-menu-collapse-btn'));
const triggers = Array.from(document.querySelectorAll('[data-pm-toggle="collapse"]'));

window.addEventListener('click', (ev) => {
  const elm = ev.target;
  if (triggers.includes(elm)) {
    const selector = elm.getAttribute('data-pm-target');
    collapse(selector, 'toggle');
  }
  if (btnTriggers.includes(elm)) {
    elm.classList[fnmap['toggle']]('active');
  }
}, false);

const fnmap = {
  'toggle': 'toggle',
  'show': 'add',
  'hide': 'remove'
};
const collapse = (selector, cmd) => {
  console.log('selectr', selector)
  const targets = Array.from(document.querySelectorAll(selector));
  targets.forEach(target => {
    target.classList[fnmap[cmd]]('show');
  });
}
