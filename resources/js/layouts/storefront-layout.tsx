import { Link, usePage } from '@inertiajs/react';
import { ShoppingCart, User, Search, Menu, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

interface Props {
    children: React.ReactNode;
}

export default function StorefrontLayout({ children }: Props) {
    const { auth } = usePage<{ auth: { user: { name: string } | null } }>().props;
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [cartCount] = useState(() => {
        if (typeof window !== 'undefined') {
            const cart = localStorage.getItem('guest_cart');
            if (cart) {
                const items = JSON.parse(cart);
                return items.length;
            }
        }
        return 0;
    });

    return (
        <div className="min-h-screen bg-background">
            {/* Header */}
            <header className="sticky top-0 z-50 border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                <div className="container mx-auto px-4">
                    <div className="flex h-16 items-center justify-between">
                        {/* Logo */}
                        <Link href="/" className="flex items-center gap-2">
                            <div className="flex h-8 w-8 items-center justify-center rounded-md bg-primary">
                                <span className="text-lg font-bold text-primary-foreground">A</span>
                            </div>
                            <span className="text-xl font-bold">Agiza Sokoni</span>
                        </Link>

                        {/* Desktop Nav */}
                        <nav className="hidden md:flex items-center gap-6">
                            <Link href="/" className="text-sm font-medium hover:text-primary">
                                Home
                            </Link>
                            <Link href="/shop" className="text-sm font-medium hover:text-primary">
                                Shop
                            </Link>
                            <Link href="/markets" className="text-sm font-medium hover:text-primary">
                                Markets
                            </Link>
                        </nav>

                        {/* Search */}
                        <div className="hidden md:flex flex-1 max-w-md mx-6">
                            <div className="relative w-full">
                                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    placeholder="Search products..."
                                    className="pl-10"
                                />
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="flex items-center gap-2">
                            <Link href="/cart">
                                <Button variant="ghost" size="icon" className="relative">
                                    <ShoppingCart className="h-5 w-5" />
                                    {cartCount > 0 && (
                                        <span className="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-xs text-primary-foreground">
                                            {cartCount}
                                        </span>
                                    )}
                                </Button>
                            </Link>

                            {auth?.user ? (
                                <Link href="/dashboard">
                                    <Button variant="ghost" size="sm">
                                        <User className="mr-2 h-4 w-4" />
                                        {auth.user.name}
                                    </Button>
                                </Link>
                            ) : (
                                <div className="flex items-center gap-2">
                                    <Link href="/login">
                                        <Button variant="ghost" size="sm">Login</Button>
                                    </Link>
                                    <Link href="/register">
                                        <Button size="sm">Sign Up</Button>
                                    </Link>
                                </div>
                            )}

                            {/* Mobile menu button */}
                            <Button
                                variant="ghost"
                                size="icon"
                                className="md:hidden"
                                onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                            >
                                {mobileMenuOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
                            </Button>
                        </div>
                    </div>

                    {/* Mobile Menu */}
                    {mobileMenuOpen && (
                        <div className="md:hidden border-t py-4">
                            <div className="flex flex-col gap-4">
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                    <Input placeholder="Search products..." className="pl-10" />
                                </div>
                                <nav className="flex flex-col gap-2">
                                    <Link href="/" className="px-2 py-1.5 text-sm font-medium hover:text-primary">
                                        Home
                                    </Link>
                                    <Link href="/shop" className="px-2 py-1.5 text-sm font-medium hover:text-primary">
                                        Shop
                                    </Link>
                                    <Link href="/markets" className="px-2 py-1.5 text-sm font-medium hover:text-primary">
                                        Markets
                                    </Link>
                                </nav>
                            </div>
                        </div>
                    )}
                </div>
            </header>

            {/* Main Content */}
            <main>{children}</main>

            {/* Footer */}
            <footer className="border-t bg-muted/50 mt-16">
                <div className="container mx-auto px-4 py-12">
                    <div className="grid gap-8 md:grid-cols-4">
                        <div>
                            <h3 className="font-semibold mb-4">Agiza Sokoni</h3>
                            <p className="text-sm text-muted-foreground">
                                Fresh produce from local markets delivered to your doorstep.
                            </p>
                        </div>
                        <div>
                            <h4 className="font-medium mb-4">Quick Links</h4>
                            <ul className="space-y-2 text-sm text-muted-foreground">
                                <li><Link href="/shop" className="hover:text-foreground">Shop</Link></li>
                                <li><Link href="/markets" className="hover:text-foreground">Markets</Link></li>
                                <li><Link href="/about" className="hover:text-foreground">About Us</Link></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-medium mb-4">Support</h4>
                            <ul className="space-y-2 text-sm text-muted-foreground">
                                <li><Link href="/contact" className="hover:text-foreground">Contact Us</Link></li>
                                <li><Link href="/faq" className="hover:text-foreground">FAQ</Link></li>
                                <li><Link href="/terms" className="hover:text-foreground">Terms of Service</Link></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-medium mb-4">Contact</h4>
                            <ul className="space-y-2 text-sm text-muted-foreground">
                                <li>Dar es Salaam, Tanzania</li>
                                <li>+255 xxx xxx xxx</li>
                                <li>info@agizasokoni.co.tz</li>
                            </ul>
                        </div>
                    </div>
                    <div className="mt-8 pt-8 border-t text-center text-sm text-muted-foreground">
                        © {new Date().getFullYear()} Agiza Sokoni. All rights reserved.
                    </div>
                </div>
            </footer>
        </div>
    );
}
