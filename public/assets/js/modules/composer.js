/* ============================================================
   composer.js  -  emoji, GIF and file buttons on a message box (component: composer).
   These checks are only for quick feedback. The server checks every file again.
   ============================================================ */
(function () {
  const EMOJI = [
    ['😀', 'grin happy smile'], ['😄', 'happy smile laugh'], ['😂', 'laugh joy tears funny'], ['🤣', 'rofl laugh funny'], ['😊', 'smile blush happy'],
    ['😍', 'love heart eyes'], ['🥰', 'love hearts'], ['😘', 'kiss love'], ['😎', 'cool sunglasses'], ['🤓', 'nerd study glasses'],
    ['🤔', 'thinking hmm question'], ['🧐', 'curious monocle inspect'], ['😅', 'sweat relief nervous'], ['😴', 'sleep tired'], ['🥱', 'yawn tired bored'],
    ['😭', 'cry sad tears'], ['😢', 'sad cry'], ['😤', 'annoyed huff'], ['😡', 'angry mad'], ['🤯', 'mind blown shocked'],
    ['😱', 'scared shock'], ['🥳', 'party celebrate'], ['🤝', 'handshake deal agree'], ['👍', 'thumbs up yes good'], ['👎', 'thumbs down no bad'],
    ['👏', 'clap applause well done'], ['🙌', 'hands celebrate praise'], ['🙏', 'thanks please pray'], ['💪', 'strong muscle effort'], ['👋', 'wave hello bye'],
    ['✌️', 'peace victory'], ['👌', 'ok perfect'], ['🤞', 'fingers crossed luck'], ['👀', 'eyes look see'], ['🧠', 'brain smart think'],
    ['❤️', 'heart love red'], ['💚', 'heart green'], ['💛', 'heart yellow'], ['💙', 'heart blue'], ['🔥', 'fire hot lit'],
    ['⭐', 'star favourite'], ['✨', 'sparkles new shiny'], ['🎉', 'party congratulations tada'], ['🎊', 'confetti celebrate'], ['🏆', 'trophy win winner'],
    ['🥇', 'gold first medal'], ['💯', 'hundred perfect'], ['✅', 'check done yes'], ['❌', 'cross wrong no'], ['⚠️', 'warning careful'],
    ['❓', 'question'], ['❗', 'important exclamation'], ['💡', 'idea light bulb tip'], ['📚', 'books study library'], ['📖', 'book read open'],
    ['📝', 'note write memo'], ['✏️', 'pencil write'], ['📎', 'paperclip attach'], ['📌', 'pin important'], ['📅', 'calendar date'],
    ['⏰', 'alarm time deadline'], ['⌛', 'hourglass time wait'], ['🎓', 'graduation student degree'], ['🏫', 'school campus university'], ['🔬', 'microscope science lab'],
    ['🧪', 'test tube chemistry lab'], ['📊', 'chart bar stats'], ['📈', 'chart up growth'], ['💻', 'laptop computer code'], ['⌨️', 'keyboard typing'],
    ['🐛', 'bug error code'], ['🚀', 'rocket launch fast'], ['☕', 'coffee break'], ['🍕', 'pizza food'], ['🎯', 'target goal'],
    ['🌟', 'star shine'], ['🌱', 'plant growth beginner'], ['🙂', 'smile slight'], ['😉', 'wink'], ['😬', 'grimace awkward'],
  ];

  document.querySelectorAll('[data-composer]').forEach((box) => {
    const text = box.querySelector('[data-composer-text]');
    const fileInput = box.querySelector('[data-composer-files]');
    const previews = box.querySelector('[data-composer-previews]');
    const gifInput = box.querySelector('[data-composer-gif]');
    const errorBox = box.querySelector('[data-composer-error]');
    const panels = box.querySelectorAll('[data-composer-panel]');
    const openers = box.querySelectorAll('[data-composer-open]');
    const allowed = box.dataset.accept.split(',').map((s) => s.slice(1));
    const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const maxFiles = Number(box.dataset.maxFiles);
    let files = [];
    let gif = null;

    const showError = (message) => {
      errorBox.hidden = !message;
      errorBox.querySelector('span').textContent = message || '';
    };
    const closePanels = () => {
      panels.forEach((p) => { p.hidden = true; });
      openers.forEach((b) => b.setAttribute('aria-expanded', 'false'));
    };

    function insertAtCursor(value) {
      const start = text.selectionStart ?? text.value.length;
      const end = text.selectionEnd ?? text.value.length;
      text.setRangeText(value, start, end, 'end');
      text.focus();
      text.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function chip(label, thumb, onRemove) {
      const item = document.createElement('div');
      item.className = 'sc-composer__chip';
      if (thumb) {
        const img = document.createElement('img');
        img.src = thumb;
        img.alt = '';
        item.append(img);
      } else {
        const icon = document.createElement('i');
        icon.className = 'bi bi-file-earmark';
        icon.setAttribute('aria-hidden', 'true');
        item.append(icon);
      }
      const name = document.createElement('span');
      name.textContent = label;
      item.append(name);
      const remove = document.createElement('button');
      remove.type = 'button';
      remove.setAttribute('aria-label', `Remove ${label}`);
      remove.innerHTML = '<i class="bi bi-x-lg" aria-hidden="true"></i>';
      remove.addEventListener('click', onRemove);
      item.append(remove);
      return item;
    }

    function paint() {
      previews.querySelectorAll('img[src^="blob:"]').forEach((img) => URL.revokeObjectURL(img.src));
      previews.textContent = '';
      const picked = new DataTransfer();
      files.forEach((file, i) => {
        picked.items.add(file);
        const ext = file.name.split('.').pop().toLowerCase();
        const thumb = imageTypes.includes(ext) ? URL.createObjectURL(file) : null;
        previews.append(chip(file.name, thumb, () => { files.splice(i, 1); showError(''); paint(); }));
      });
      fileInput.files = picked.files;
      if (gif) {
        previews.append(chip(gif.title, gif.preview, () => { gif = null; gifInput.value = ''; paint(); }));
      }
    }

    function addFiles(list) {
      showError('');
      for (const file of list) {
        const ext = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
        if (!allowed.includes(ext)) { showError(`"${file.name}" isn't an allowed file type.`); continue; }
        const limit = Number(imageTypes.includes(ext) ? box.dataset.imageMax : box.dataset.fileMax);
        if (file.size > limit) { showError(`"${file.name}" is larger than ${Math.round(limit / 1048576)} MB.`); continue; }
        if (files.length >= maxFiles) { showError(`You can share up to ${maxFiles} files in one post.`); break; }
        files.push(file);
      }
      paint();
    }

    box.querySelector('[data-composer-attach]').addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
      const chosen = [...fileInput.files];
      addFiles(chosen);
    });
    ['dragenter', 'dragover'].forEach((type) => box.addEventListener(type, (e) => { e.preventDefault(); box.classList.add('is-dragover'); }));
    ['dragleave', 'drop'].forEach((type) => box.addEventListener(type, (e) => {
      e.preventDefault();
      if (type === 'drop' || !box.contains(e.relatedTarget)) box.classList.remove('is-dragover');
    }));
    box.addEventListener('drop', (e) => addFiles(e.dataTransfer.files));

    /* Panels */
    let gifsLoaded = false;
    openers.forEach((button) => button.addEventListener('click', () => {
      const name = button.dataset.composerOpen;
      const panel = box.querySelector(`[data-composer-panel="${name}"]`);
      const opening = panel.hidden;
      closePanels();
      if (!opening) return;
      panel.hidden = false;
      button.setAttribute('aria-expanded', 'true');
      panel.querySelector('input')?.focus();
      if (name === 'gif' && !gifsLoaded) { gifsLoaded = true; searchGifs(''); }
    }));
    document.addEventListener('click', (e) => { if (!box.contains(e.target)) closePanels(); });
    box.addEventListener('keydown', (e) => { if (e.key === 'Escape') closePanels(); });

    /* Emoji */
    const emojiGrid = box.querySelector('[data-emoji-grid]');
    const paintEmoji = (query = '') => {
      emojiGrid.textContent = '';
      const q = query.trim().toLowerCase();
      EMOJI.filter(([, words]) => !q || words.includes(q)).forEach(([emoji, words]) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.textContent = emoji;
        b.title = words.split(' ')[0];
        b.setAttribute('aria-label', words.split(' ')[0]);
        b.addEventListener('click', () => insertAtCursor(emoji));
        emojiGrid.append(b);
      });
    };
    paintEmoji();
    box.querySelector('[data-emoji-search]').addEventListener('input', (e) => paintEmoji(e.target.value));
    box.querySelector('[data-emoji-search]').addEventListener('keydown', (e) => { if (e.key === 'Enter') e.preventDefault(); });

    /* GIFs */
    const gifGrid = box.querySelector('[data-gif-grid]');
    const gifSearch = box.querySelector('[data-gif-search]');
    let searchTimer = null;
    let searchToken = 0;
    function gifMessage(message) {
      gifGrid.textContent = '';
      const p = document.createElement('p');
      p.className = 'sc-composer__empty';
      p.textContent = message;
      gifGrid.append(p);
    }
    async function searchGifs(q) {
      const token = ++searchToken;
      gifMessage('Loading…');
      const url = new URL(box.dataset.gifUrl, window.location.href);
      if (q) url.searchParams.set('q', q);
      const res = await SC.request(url);
      if (token !== searchToken) return;
      if (!res.success) { gifMessage(res.message || "GIFs aren't available right now."); return; }
      gifGrid.textContent = '';
      if (!res.data.gifs.length) { gifMessage('No GIFs found.'); return; }
      res.data.gifs.forEach((item) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.setAttribute('aria-label', item.title);
        const img = document.createElement('img');
        img.src = item.preview;
        img.alt = '';
        img.loading = 'lazy';
        img.referrerPolicy = 'no-referrer';
        b.append(img);
        b.addEventListener('click', () => {
          gif = item;
          gifInput.value = item.id;
          closePanels();
          paint();
        });
        gifGrid.append(b);
      });
    }
    if (gifSearch) {
      gifSearch.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => searchGifs(gifSearch.value.trim()), 350);
      });
      gifSearch.addEventListener('keydown', (e) => { if (e.key === 'Enter') e.preventDefault(); });
    }
  });
})();
