'use client';

import { useState } from 'react';

const seedStudents = [
  { id: 'STU-1002', name: 'Rohit Sharma', phone: '9876543211', room: '102', status: 'Paid' },
  { id: 'STU-1003', name: 'Meera Patel', phone: '9876543212', room: '201', status: 'Due' },
  { id: 'STU-1005', name: 'Aarav Singh', phone: '9844370331', room: '203', status: 'Paid' }
];

const seedRooms = [
  { number: '101', type: 'Single', capacity: 1, occupied: 0, status: 'Available' },
  { number: '102', type: 'Double', capacity: 2, occupied: 1, status: 'Available' },
  { number: '201', type: 'Double', capacity: 2, occupied: 2, status: 'Occupied' },
  { number: '203', type: 'Triple', capacity: 3, occupied: 1, status: 'Available' }
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
  const [students, setStudents] = useState(seedStudents);
  const [rooms] = useState(seedRooms);

  function login(event) {
    event.preventDefault();
    const admin = role === 'admin' && username === 'admin' && password === 'admin123';
    const resident = students.find((item) => role === 'resident' && item.id === username && item.phone === password);
    if (admin || resident) { setView(role === 'admin' ? 'overview' : 'resident'); setNotice(''); return; }
    setNotice(role === 'admin' ? 'Use admin / admin123.' : 'Use a registered Student ID and phone number.');
  }

  if (view === 'login') return <Login role={role} setRole={setRole} username={username} setUsername={setUsername} password={password} setPassword={setPassword} notice={notice} onSubmit={login} />;
  if (view === 'resident') return <ResidentPortal student={students.find((item) => item.id === username)} onLogout={() => setView('login')} />;
  return <AdminPortal view={view} setView={setView} students={students} setStudents={setStudents} rooms={rooms} onLogout={() => setView('login')} />;
}

function Login({ role, setRole, username, setUsername, password, setPassword, notice, onSubmit }) {
  return <main className="login-shell"><section className="login-hero"><div className="mark">⌂</div><span className="kicker">Hostel operations platform</span><h1>Run your residence with clarity.</h1><p>One calm workspace for students, rooms, payments, visitors, and everyday hostel operations.</p><div className="hero-points"><span>✓ Manage residents and rooms</span><span>✓ Keep fees and requests organized</span></div></section><section className="login-panel"><span className="kicker blue">Secure access</span><h2>Welcome back</h2><p className="muted">Sign in to your {role === 'admin' ? 'admin dashboard' : 'resident portal'}.</p><div className="segmented"><button className={role === 'admin' ? 'selected' : ''} onClick={() => setRole('admin')}>Admin</button><button className={role === 'resident' ? 'selected' : ''} onClick={() => setRole('resident')}>Resident</button></div><form onSubmit={onSubmit}><label>{role === 'admin' ? 'Username' : 'Student ID'}<input value={username} onChange={(event) => setUsername(event.target.value)} placeholder={role === 'admin' ? 'admin' : 'STU-1002'} required /></label><label>{role === 'admin' ? 'Password' : 'Registered phone'}<input type={role === 'admin' ? 'password' : 'tel'} value={password} onChange={(event) => setPassword(event.target.value)} placeholder={role === 'admin' ? 'admin123' : '9876543211'} required /></label>{notice && <div className="notice">{notice}</div>}<button className="primary" type="submit">Open portal <span>→</span></button></form><small className="demo">Demo: admin / admin123</small></section></main>;
}

function AdminPortal({ view, setView, students, setStudents, rooms, onLogout }) {
  const [filter, setFilter] = useState('');
  const [showForm, setShowForm] = useState(false);
  const [newStudent, setNewStudent] = useState({ id: '', name: '', phone: '', room: '', status: 'Paid' });
  const nav = [{ id: 'overview', label: 'Overview', icon: '▦' }, { id: 'students', label: 'Students', icon: '♙' }, { id: 'rooms', label: 'Rooms', icon: '⌂' }, { id: 'payments', label: 'Payments', icon: '₹' }, { id: 'requests', label: 'Requests', icon: '⚑' }];
  const addStudent = (event) => { event.preventDefault(); setStudents([...students, newStudent]); setNewStudent({ id: '', name: '', phone: '', room: '', status: 'Paid' }); setShowForm(false); setView('students'); };
  return <main className="app-shell"><aside><div className="brand"><span className="mark small">⌂</span>HostelMS</div><nav>{nav.map((item) => <button key={item.id} className={view === item.id ? 'nav-active' : ''} onClick={() => setView(item.id)}>{item.icon} {item.label}</button>)}</nav><button className="logout" onClick={onLogout}>↪ Sign out</button></aside><section className="workspace"><header><div><span className="kicker blue">Admin workspace</span><h1>{nav.find((item) => item.id === view)?.label || 'Overview'}</h1><p className="muted">Manage your hostel operations from one workspace.</p></div><span className="profile-chip">SA</span></header>{view === 'overview' && <Overview students={students} rooms={rooms} filter={filter} setFilter={setFilter} />} {view === 'students' && <Students students={students} filter={filter} setFilter={setFilter} showForm={showForm} setShowForm={setShowForm} newStudent={newStudent} setNewStudent={setNewStudent} addStudent={addStudent} />} {view === 'rooms' && <Rooms rooms={rooms} />} {view === 'payments' && <Payments students={students} />} {view === 'requests' && <Requests />}</section></main>;
}

function Overview({ students, rooms, filter, setFilter }) { const visible = activities.filter((item) => item.join(' ').toLowerCase().includes(filter.toLowerCase())); return <><div className="stats"><Stat label="Total students" value={students.length} icon="♙" /><Stat label="Total rooms" value={rooms.length} icon="⌂" /><Stat label="Occupied rooms" value={rooms.filter((room) => room.occupied > 0).length} icon="▣" /><Stat label="Monthly revenue" value="₹24,000" icon="₹" /></div><div className="content-grid"><section className="panel wide"><div className="panel-head"><div><h2>Recent activity</h2><p className="muted">Live overview of hostel operations</p></div><label className="search">⌕<input value={filter} onChange={(event) => setFilter(event.target.value)} placeholder="Filter activity" /></label></div><div className="activity-list">{visible.map(([type, title, date]) => <div className="activity" key={title}><span className="activity-icon">{type === 'Payment' ? '₹' : type === 'Request' ? '!' : '♙'}</span><div><strong>{title}</strong><small>{type} · {date}</small></div><span className="arrow">→</span></div>)}</div></section><section className="panel"><div className="panel-head"><div><h2>Quick actions</h2><p className="muted">Common admin tasks</p></div></div><div className="quick-actions"><button>Add student</button><button>Record payment</button><button>Allocate room</button><button>Review requests</button></div></section></div></>; }

function Students({ students, filter, setFilter, showForm, setShowForm, newStudent, setNewStudent, addStudent }) { const visible = students.filter((student) => `${student.id} ${student.name} ${student.phone}`.toLowerCase().includes(filter.toLowerCase())); return <><div className="toolbar"><label className="search"><span>⌕</span><input value={filter} onChange={(event) => setFilter(event.target.value)} placeholder="Search students" /></label><button className="primary" onClick={() => setShowForm(!showForm)}>{showForm ? 'Close form' : '+ Add student'}</button></div>{showForm && <form className="panel student-form" onSubmit={addStudent}><h2>Add student</h2><div className="form-grid"><input placeholder="Student ID" value={newStudent.id} onChange={(event) => setNewStudent({ ...newStudent, id: event.target.value })} required /><input placeholder="Full name" value={newStudent.name} onChange={(event) => setNewStudent({ ...newStudent, name: event.target.value })} required /><input placeholder="Phone number" value={newStudent.phone} onChange={(event) => setNewStudent({ ...newStudent, phone: event.target.value })} required /><input placeholder="Room number" value={newStudent.room} onChange={(event) => setNewStudent({ ...newStudent, room: event.target.value })} required /></div><button className="primary" type="submit">Save student</button></form>}<section className="panel table-panel"><div className="table-wrap"><table><thead><tr><th>Student ID</th><th>Name</th><th>Phone</th><th>Room</th><th>Fee status</th></tr></thead><tbody>{visible.map((student) => <tr key={student.id}><td><strong>{student.id}</strong></td><td>{student.name}</td><td>{student.phone}</td><td>{student.room || 'Unassigned'}</td><td><span className={student.status === 'Paid' ? 'badge paid' : 'badge due'}>{student.status}</span></td></tr>)}</tbody></table></div></section></>; }

function Rooms({ rooms }) { return <section className="panel table-panel"><div className="panel-head"><div><h2>Room inventory</h2><p className="muted">Occupancy and availability at a glance</p></div></div><div className="room-grid">{rooms.map((room) => <article className="room-card" key={room.number}><span className="room-number">{room.number}</span><strong>{room.type}</strong><small>{room.occupied}/{room.capacity} occupied</small><span className={room.occupied < room.capacity ? 'badge paid' : 'badge due'}>{room.occupied < room.capacity ? 'Available' : 'Full'}</span></article>)}</div></section>; }
function Payments({ students }) { return <section className="panel table-panel"><div className="panel-head"><div><h2>Payment records</h2><p className="muted">Latest fee transactions</p></div></div><div className="table-wrap"><table><thead><tr><th>Receipt</th><th>Student</th><th>Month</th><th>Amount</th><th>Status</th></tr></thead><tbody>{students.map((student, index) => <tr key={student.id}><td>#RC-{2041 - index}</td><td>{student.name}</td><td>September 2026</td><td>₹8,000</td><td><span className={student.status === 'Paid' ? 'badge paid' : 'badge due'}>{student.status}</span></td></tr>)}</tbody></table></div></section>; }
function Requests() { return <section className="panel"><div className="panel-head"><div><h2>Support requests</h2><p className="muted">Messages from residents</p></div></div><div className="request"><span className="activity-icon">!</span><div><strong>Room maintenance request</strong><small>Open · Submitted by STU-1003</small></div><button className="outline">Review</button></div><div className="request"><span className="activity-icon">₹</span><div><strong>Payment question</strong><small>In progress · Submitted by STU-1002</small></div><button className="outline">Review</button></div></section>; }
function Stat({ label, value, icon }) { return <div className="stat"><span className="stat-icon">{icon}</span><div><small>{label}</small><strong>{value}</strong></div></div>; }
function ResidentPortal({ student, onLogout }) { const [message, setMessage] = useState(''); return <main className="app-shell"><aside><div className="brand"><span className="mark small">⌂</span>HostelMS</div><nav><button className="nav-active">▦ My overview</button></nav><button className="logout" onClick={onLogout}>↪ Sign out</button></aside><section className="workspace"><header><div><span className="kicker blue">Resident portal</span><h1>Welcome, {student.name}</h1><p className="muted">Your room, payment, and support information at a glance.</p></div><span className="profile-chip">{student.id}</span></header><div className="stats"><Stat label="Room" value={student.room} icon="⌂" /><Stat label="Fee status" value={student.status} icon="₹" /><Stat label="Next payment" value="30 Sep" icon="◷" /></div><div className="content-grid"><section className="panel wide"><div className="panel-head"><div><h2>My payment history</h2><p className="muted">Recent transactions and receipts</p></div></div><div className="payment-row"><strong>#RC-2041</strong><span>September 2026</span><b>₹8,000</b><em className="paid">Paid</em></div><div className="payment-row"><strong>#RC-1930</strong><span>August 2026</span><b>₹8,000</b><em className="paid">Paid</em></div></section><section className="panel"><div className="panel-head"><div><h2>Message admin</h2><p className="muted">Ask about your stay</p></div></div><textarea value={message} onChange={(event) => setMessage(event.target.value)} placeholder="Write your message..." /><button className="primary" onClick={() => setMessage('Message sent to admin ✓')}>Send message →</button></section></div></section></main>; }
