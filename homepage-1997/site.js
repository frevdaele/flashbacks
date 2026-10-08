// Vervangt de Java-applet "fader20" (1997): wisselende welkomstteksten die in- en uitfaden.
(function () {
  var el = document.querySelector('.fader');
  if (!el || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  var teksten = ['Welcome dear guest', 'to my little spot on the Net.',
                 "Don't hesitate to contact me !", 'I hope you will enjoy this page'];
  var i = 0;
  setInterval(function () {
    el.classList.add('weg');
    setTimeout(function () {
      i = (i + 1) % teksten.length;
      el.textContent = teksten[i];
      el.classList.remove('weg');
    }, 450);
  }, 3500);
})();
