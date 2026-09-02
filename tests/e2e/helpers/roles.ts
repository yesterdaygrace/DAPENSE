/** Role matrix mirrors App\Livewire\Concerns\HasRole::canAccess */
export type Usertype = 'rootsuperuser' | 'admin' | 'operator' | 'bod';

export const ROLE_MAP: Record<Usertype, { email: string; password: string; name: string }> = {
  rootsuperuser: { email: 'e2e_rootsuperuser@dapense.test', password: 'password', name: 'E2E Root' },
  admin: { email: 'e2e_admin@dapense.test', password: 'password', name: 'E2E Admin' },
  operator: { email: 'e2e_operator@dapense.test', password: 'password', name: 'E2E Operator' },
  bod: { email: 'e2e_bod@dapense.test', password: 'password', name: 'E2E BOD' },
};

export const PERMISSIONS: Record<string, Usertype[]> = {
  dashboard: ['rootsuperuser', 'admin', 'operator', 'bod'],
  'master-data': ['rootsuperuser', 'admin', 'operator'],
  transactions: ['rootsuperuser', 'admin', 'operator'],
  reports: ['rootsuperuser', 'admin', 'operator', 'bod'],
  finance: ['rootsuperuser', 'admin', 'operator', 'bod'],
  administration: ['rootsuperuser', 'admin'],
  settings: ['rootsuperuser', 'admin', 'operator'],
  'jurnal-entry': ['rootsuperuser', 'admin', 'operator'],
  jurnaling: ['rootsuperuser', 'admin', 'operator', 'bod'],
  bukubesar: ['rootsuperuser', 'admin', 'operator', 'bod'],
  neracasaldo: ['rootsuperuser', 'admin', 'operator', 'bod'],
  posting: ['rootsuperuser', 'admin'],
  otorisator: ['rootsuperuser', 'admin', 'operator'],
  users: ['rootsuperuser', 'admin'],
  saldoawal: ['rootsuperuser', 'admin', 'operator'],
  periodes: ['rootsuperuser', 'admin', 'operator'],
  'coa-workspace': ['rootsuperuser', 'admin', 'operator'],
};

export function canAccess(role: Usertype, feature: string): boolean {
  return (PERMISSIONS[feature] ?? []).includes(role);
}
