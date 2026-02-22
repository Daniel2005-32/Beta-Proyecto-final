import { ShoppingBag, Menu, X } from "lucide-react";
import { useState } from "react";

export function Header() {
  const [menuOpen, setMenuOpen] = useState(false);

  const scrollToSection = (id: string) => {
    const element = document.getElementById(id);
    element?.scrollIntoView({ behavior: 'smooth' });
    setMenuOpen(false);
  };

  return (
    <header className="fixed top-0 left-0 right-0 bg-gray-900/95 backdrop-blur-sm shadow-lg z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <div className="flex items-center gap-2 cursor-pointer" onClick={() => scrollToSection('inicio')}>
            <ShoppingBag className="w-8 h-8 text-purple-500" />
            <span className="text-xl font-semibold text-white">GameAnime<span className="text-purple-500">Store</span></span>
          </div>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex items-center gap-8">
            <button onClick={() => scrollToSection('inicio')} className="text-gray-300 hover:text-purple-500 transition-colors">
              Inicio
            </button>
            <button onClick={() => scrollToSection('productos')} className="text-gray-300 hover:text-purple-500 transition-colors">
              Productos
            </button>
            <button onClick={() => scrollToSection('categorias')} className="text-gray-300 hover:text-purple-500 transition-colors">
              Categorías
            </button>
            <button onClick={() => scrollToSection('ofertas')} className="text-gray-300 hover:text-purple-500 transition-colors">
              Ofertas
            </button>
            <button onClick={() => scrollToSection('contacto')} className="text-gray-300 hover:text-purple-500 transition-colors">
              Contacto
            </button>
          </nav>

          {/* Mobile Menu Button */}
          <button 
            className="md:hidden p-2 text-white"
            onClick={() => setMenuOpen(!menuOpen)}
          >
            {menuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>

        {/* Mobile Navigation */}
        {menuOpen && (
          <nav className="md:hidden py-4 flex flex-col gap-4 border-t border-gray-800">
            <button onClick={() => scrollToSection('inicio')} className="text-gray-300 hover:text-purple-500 transition-colors text-left">
              Inicio
            </button>
            <button onClick={() => scrollToSection('productos')} className="text-gray-300 hover:text-purple-500 transition-colors text-left">
              Productos
            </button>
            <button onClick={() => scrollToSection('categorias')} className="text-gray-300 hover:text-purple-500 transition-colors text-left">
              Categorías
            </button>
            <button onClick={() => scrollToSection('ofertas')} className="text-gray-300 hover:text-purple-500 transition-colors text-left">
              Ofertas
            </button>
            <button onClick={() => scrollToSection('contacto')} className="text-gray-300 hover:text-purple-500 transition-colors text-left">
              Contacto
            </button>
          </nav>
        )}
      </div>
    </header>
  );
}
