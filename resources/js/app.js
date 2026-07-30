import './bootstrap';

import Alpine from 'alpinejs';
import { createIcons } from 'lucide';
import {
  LayoutDashboard, Database, ArrowLeftRight, BarChart3, DollarSign,
  Shield, Settings, LogOut, Menu, Home, ChevronRight, ChevronDown, User,
  FileText, BookOpen, Receipt, Calculator, Calendar, Grid3x3, Wallet,
  History, Search, LogIn, PlusCircle, Edit, UserPlus, ArrowRight, Plus,
  Building, Filter, Trash, Download, Save, Check, AlertCircle,
  AlertTriangle, Info, X, Lock, Mail, Code, Server, Package, Clock,
  List, Copy, CalendarCheck, ArrowUpDown, ArrowDown, ArrowUp, CheckCircle,
  ShieldHalf,
} from 'lucide';

window.Alpine = Alpine;

const icons = {
  'layout-dashboard': LayoutDashboard,
  'database': Database,
  'arrow-left-right': ArrowLeftRight,
  'bar-chart-3': BarChart3,
  'dollar-sign': DollarSign,
  'shield': Shield,
  'shield-half': ShieldHalf,
  'settings': Settings,
  'log-out': LogOut,
  'menu': Menu,
  'home': Home,
  'chevron-right': ChevronRight,
  'chevron-down': ChevronDown,
  'user': User,
  'file-text': FileText,
  'book-open': BookOpen,
  'receipt': Receipt,
  'calculator': Calculator,
  'calendar': Calendar,
  'grid-3x3': Grid3x3,
  'wallet': Wallet,
  'history': History,
  'search': Search,
  'log-in': LogIn,
  'plus-circle': PlusCircle,
  'edit': Edit,
  'user-plus': UserPlus,
  'arrow-right': ArrowRight,
  'plus': Plus,
  'building': Building,
  'filter': Filter,
  'trash': Trash,
  'download': Download,
  'save': Save,
  'check': Check,
  'alert-circle': AlertCircle,
  'alert-triangle': AlertTriangle,
  'info': Info,
  'x': X,
  'lock': Lock,
  'mail': Mail,
  'code': Code,
  'server': Server,
  'package': Package,
  'clock': Clock,
  'list': List,
  'copy': Copy,
  'calendar-check': CalendarCheck,
  'arrow-up-down': ArrowUpDown,
  'arrow-down': ArrowDown,
  'arrow-up': ArrowUp,
  'check-circle': CheckCircle,
};

// Initialize Lucide icons
createIcons({ icons });

// Reinitialize after Livewire navigations (SPA)
document.addEventListener('livewire:navigated', () => {
  createIcons({ icons });
});

Alpine.start();
