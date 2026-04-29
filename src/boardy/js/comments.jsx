const { useState, useEffect } = React;

const API = 'https://api.kilqa.ai-info.ru';
const POST_ID = 1;

function CommentsApp() {
  const [items, setItems] = useState([]);
  const [text, setText] = useState('');
  const [editId, setEditId] = useState(null);
  const [editText, setEditText] = useState('');
  const [error, setError] = useState('');
  const [jwt, setJwt] = useState(null);

  // Получаем JWT по куке PHPSESSID при загрузке
  useEffect(() => {
    fetch('/api/me.php', { credentials: 'include' })
      .then(r => { if (!r.ok) return null; return r.json(); })
      .then(data => { if (data && data.token) setJwt(data.token); })
      .catch(() => setJwt(null));
  }, []);

  const load = async () => {
    try {
      setError('');
      const res = await fetch(`${API}/api/posts/${POST_ID}/comments`);
      const data = await res.json();
      setItems(data.items || []);
    } catch (e) {
      setError('Не удалось загрузить комментарии');
    }
  };

  useEffect(() => {
    load();
  }, []);

  // Формирует заголовки с Bearer если есть JWT
  const authHeaders = () => {
    const headers = { 'Content-Type': 'application/json' };
    if (jwt) headers['Authorization'] = 'Bearer ' + jwt;
    return headers;
  };

  const add = async () => {
    if (!text.trim()) return;
    try {
      setError('');
      const res = await fetch(`${API}/api/posts/${POST_ID}/comments`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ body: text })
      });
      if (!res.ok) {
        const err = await res.json();
        setError(err.detail || 'Ошибка создания');
        return;
      }
      setText('');
      load();
    } catch (e) {
      setError('Сервер недоступен');
    }
  };

  const save = async (id) => {
    if (!editText.trim()) return;
    try {
      setError('');
      const res = await fetch(`${API}/api/comments/${id}`, {
        method: 'PUT',
        headers: authHeaders(),
        body: JSON.stringify({ body: editText })
      });
      if (!res.ok) {
        const err = await res.json();
        setError(err.detail || 'Ошибка обновления');
        return;
      }
      setEditId(null);
      setEditText('');
      load();
    } catch (e) {
      setError('Сервер недоступен');
    }
  };

  const del = async (id) => {
    if (!confirm('Удалить комментарий?')) return;
    try {
      setError('');
      const res = await fetch(`${API}/api/comments/${id}`, {
        method: 'DELETE',
        headers: authHeaders()
      });
      if (!res.ok) {
        const err = await res.json();
        setError(err.detail || 'Ошибка удаления');
        return;
      }
      load();
    } catch (e) {
      setError('Сервер недоступен');
    }
  };

  return (
    <div>
      {error && (
        <div className="alert alert-danger">{error}</div>
      )}

      {items.length === 0 ? (
        <div className="alert alert-secondary">Комментариев пока нет</div>
      ) : (
        items.map(item => (
          <div key={item.id} className="card mb-3">
            <div className="card-body">
              <div className="d-flex justify-content-between align-items-start gap-3">
                <div className="flex-grow-1">
                  <h5 className="card-title mb-1">{item.author_name}</h5>
                  <div className="text-muted small mb-2">{item.created_at}</div>

                  {editId === item.id ? (
                    <div className="input-group">
                      <input
                        className="form-control"
                        value={editText}
                        onChange={e => setEditText(e.target.value)}
                      />
                      <button className="btn btn-success" onClick={() => save(item.id)}>
                        Сохранить
                      </button>
                      <button className="btn btn-secondary" onClick={() => {
                        setEditId(null); setEditText('');
                      }}>
                        Отмена
                      </button>
                    </div>
                  ) : (
                    <p className="card-text">{item.body}</p>
                  )}
                </div>

                {editId !== item.id && (
                  <div className="d-flex gap-2">
                    <button className="btn btn-sm btn-outline-secondary" onClick={() => {
                      setEditId(item.id); setEditText(item.body);
                    }}>✏️</button>
                    <button className="btn btn-sm btn-outline-danger" onClick={() => del(item.id)}>
                      🗑️
                    </button>
                  </div>
                )}
              </div>
            </div>
          </div>
        ))
      )}

      {!jwt && (
        <div className="alert alert-warning mt-3">
          <a href="/login.php">Войдите</a>, чтобы оставить комментарий.
        </div>
      )}

      {jwt && (
        <div className="input-group mt-4">
          <input
            className="form-control"
            placeholder="Введите комментарий"
            value={text}
            onChange={e => setText(e.target.value)}
          />
          <button className="btn btn-primary" onClick={add}>
            Отправить
          </button>
        </div>
      )}
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('app')).render(<CommentsApp />);
