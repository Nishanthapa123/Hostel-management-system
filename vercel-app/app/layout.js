import './globals.css';

export const metadata = {
  title: 'HostelMS | Residence Operations',
  description: 'A modern hostel management portal.'
};

export default function RootLayout({ children }) {
  return <html lang="en"><body>{children}</body></html>;
}
