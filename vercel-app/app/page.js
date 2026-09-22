'use client';

import { useState } from 'react';

const residents = [
  { id: 'STU-1002', name: 'Rohit Sharma', phone: '9876543211', room: '102', status: 'Paid' },
  { id: 'STU-1003', name: 'Meera Patel', phone: '9876543212', room: '201', status: 'Due' },
  { id: 'STU-1005', name: 'Aarav Singh', phone: '9844370331', room: '203', status: 'Paid' }
];

const activities = [
  ['Student', 'Rohit Sharma checked in', 'Today'],
  ['Payment', 'Fee receipt #RC-2041 recorded', 'Yesterday'],
  ['Request', 'Room maintenance request opened', '2 days ago']
];

export default function Home() {
  const [view, setView] = useState('login');
  const [role, setRole] = useState('admin');
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [notice, setNotice] = useState('');
  const [filter, setFilter] = useState('');
  const [message, setMessage] = useState('');

  function login(event) {
    event.preventDefault();
    const validAdmin = role === 'admin' && username === 'admin' && password === 'admin123';
    const resident = residents.find((item) => role === 'resident' && item.id === username && item.phone === password);
    if (validAdmin || resident) {
      setView(role === 'admin' ? 'admin' : 'resident');
      setNotice('');
      return;
    }
    setNotice(role === 'admin' ? 'Use admin / admin123.' : 'Use a registered Student ID and phone number.');
  }

  if (view === 'login') {
    return <main className="login-shell"><section className="login-hero"><div className="mark">⌂</div><span className="kicker">Hostel operations platform</span><h1>Run your residence with clarity.</h1><p>One calm workspace for students, rooms, payments, visitors, and everyday hostel operations.</p><div className="hero-points"><span>✓ Manage residents and rooms</span><span>✓ Keep fees and requests organized</span></div></section><section className="login-panel"><span className="kicker blue">Secure access</span><h2>Welcome back</h2><p className="muted">Sign in to your {role === 'admin' ? 'admin dashboard' : 'resident portal'}.</p><div className="segmented"><button className={role === 'admin' ? 'selected' : ''} onClick={() => { setRole('admin'); setNotice(''); }}>Admin</button><button className={role === 'resident' ? 'selected' : ''} onClick={() => { setRole('resident'); setNotice(''); }}>Resident</button></div><form onSubmit={login}><label>{role === 'admin' ? 'Username' : 'Student ID'}<input value={username} onChange={(event) => setUsername(event.target.value)} placeholder={role === 'admin' ? 'admin' : 'STU-1002'} required /></label><label>{role === 'admin' ? 'Password' : 'Registered phone'}<input type={role === 'admin' ? 'password' : 'tel'} value={password} onChange={(event) => setPassword(event.target.value)} placeholder={role === 'admin' ? 'admin123' : '9876543211'} required /></label>{notice && <div className="notice">{notice}</div>}<button className="primary" type="submit">Open portal <span>→</span></button></form><small className="demo">Demo: admin / admin123</small></section></main>;
  }

  const visibleActivities = activities.filter((item) => item.join(' ').toLowerCase().includes(filter.toLowerCase()));
  const resident = residents.find((item) => item.id === username);
  return <main className="app-shell"><aside><div className="brand"><span className="mark small">⌂</span>HostelMS</div><nav><button className="nav-active">▦ Overview</button><button>♙ Students</button><button>⌂ Rooms</button><button>◈ Payments</button><button>⚑ Requests</button></nav><button className="logout" onClick={() => { setView('login'); setUsername(''); setPassword(''); }}>↪ Sign out</button></aside><section className="workspace"><header><div><span className="kicker blue">{view === 'admin' ? 'Admin workspace' : 'Resident portal'}</span><h1>{view === 'admin' ? 'Good morning, Administrator' : `Welcome, ${resident?.name}`}</h1><p className="muted">{view === 'admin' ? 'Here is what is happening across your hostel today.' : 'Your room, payment, and support information at a glance.'}</p></div><span className="profile-chip">{view === 'admin' ? 'SA' : resident?.id}</span></header>{view === 'admin' ? <><div className="stats"><Stat label="Total students" value="3" icon="♙" /><Stat label="Total rooms" value="4" icon="⌂" /><Stat label="Occupied rooms" value="3" icon="▣" /><Stat label="Monthly revenue" value="₹24,000" icon="₹" /></div><div className="content-grid"><section className="panel wide"><div className="panel-head"><div><h2>Recent activity</h2><p className="muted">Live overview of hostel operations</p></div><label className="search">⌕<input value={filter} onChange={(event) => setFilter(event.target.value)} placeholder="Filter activity" /></label></div><div className="activity-list">{visibleActivities.map(([type, title, date]) => <div className="activity" key={title}><span className="activity-icon">{type === 'Payment' ? '₹' : type === 'Request' ? '!' : '♙'}</span><div><strong>{title}</strong><small>{type} · {date}</small></div><span className="arrow">→</span></div>)}</div></section><section className="panel"><div className="panel-head"><div><h2>Quick actions</h2><p className="muted">Common admin tasks</p></div></div><div className="quick-actions"><button>＋ Add student</button><button>＋ Record payment</button><button>＋ Allocate room</button><button>＋ Review requests</button></div></section></div></> : <ResidentView resident={resident} message={message} setMessage={setMessage} />}</section></main>;
}

function Stat({ label, value, icon }) { return <div className="stat"><span className="stat-icon">{icon}</span><div><small>{label}</small><strong>{value}</strong></div></div>; }
function ResidentView({ resident, message, setMessage }) { return <><div className="stats"><Stat label="Room" value={resident.room} icon="⌂" /><Stat label="Fee status" value={resident.status} icon="₹" /><Stat label="Next payment" value="30 Sep" icon="◷" /></div><div className="content-grid"><section className="panel wide"><div className="panel-head"><div><h2>My payment history</h2><p className="muted">Recent transactions and receipts</p></div></div><div className="payment-row"><strong>#RC-2041</strong><span>September 2026</span><b>₹8,000</b><em className="paid">Paid</em></div><div className="payment-row"><strong>#RC-1930</strong><span>August 2026</span><b>₹8,000</b><em className="paid">Paid</em></div></section><section className="panel"><div className="panel-head"><div><h2>Message admin</h2><p className="muted">Ask about your stay</p></div></div><textarea value={message} onChange={(event) => setMessage(event.target.value)} placeholder="Write your message..." /><button className="primary" onClick={() => setMessage('Message sent to admin ✓')}>Send message →</button></section></div></>; }
