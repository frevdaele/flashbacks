// Filter op titel in de artikel- en filmlijst
(function () {
  var veld = document.getElementById('filter');
  if (!veld) return;
  var norm = function (s) { return s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase(); };
  veld.addEventListener('input', function () {
    var q = norm(veld.value.trim());
    document.querySelectorAll('.jaar').forEach(function (jaar) {
      var zichtbaar = 0;
      jaar.querySelectorAll('li').forEach(function (li) {
        var ok = !q || norm(li.textContent).indexOf(q) !== -1;
        li.hidden = !ok;
        if (ok) zichtbaar++;
      });
      jaar.hidden = zichtbaar === 0;
    });
  });
})();
