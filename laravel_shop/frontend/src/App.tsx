import { Header } from "./components/Header";
import { Hero } from "./components/Hero";
import { ProductCard } from "./components/ProductCard";
import { CategoryCard } from "./components/CategoryCard";
import { Footer } from "./components/Footer";
import { Star, Truck, Shield, Headphones } from "lucide-react";

export default function App() {
  const featuredProducts = [
    {
      name: "PlayStation 5 Console",
      price: 499,
      originalPrice: 549,
      image: "https://images.unsplash.com/photo-1644571580646-7048372c491a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxwbGF5c3RhdGlvbiUyMGNvbnRyb2xsZXIlMjBjb25zb2xlfGVufDF8fHx8MTc3MDk3NjMxMnww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
      category: "Consolas"
    },
    {
      name: "Nintendo Switch OLED",
      price: 349,
      image: "https://images.unsplash.com/photo-1676261233849-0755de764396?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxuaW50ZW5kbyUyMHN3aXRjaCUyMGdhbWluZ3xlbnwxfHx8fDE3NzA5MDU1NzV8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
      category: "Consolas"
    },
    {
      name: "Auriculares Gaming RGB",
      price: 89,
      originalPrice: 129,
      image: "https://images.unsplash.com/photo-1640823127518-65e1ad563576?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBoZWFkc2V0JTIwZXNwb3J0c3xlbnwxfHx8fDE3NzA5NzYzMTN8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
      category: "Accesorios"
    },
    {
      name: "Teclado Mecánico RGB",
      price: 119,
      image: "https://images.unsplash.com/photo-1645802106095-765b7e86f5bb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBrZXlib2FyZCUyMHJnYnxlbnwxfHx8fDE3NzA5NDEzOTB8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
      category: "Accesorios"
    },
    {
      name: "Figuras Anime Colección",
      price: 45,
      originalPrice: 65,
      image: "https://images.unsplash.com/photo-1765633358966-45a72a11fdaa?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxhbmltZSUyMGZpZ3VyaW5lJTIwY29sbGVjdGlibGV8ZW58MXx8fHwxNzcwOTA4Njg2fDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
      category: "Merchandising"
    },
    {
      name: "Manga Box Set",
      price: 89,
      image: "https://images.unsplash.com/photo-1760113426097-a4076e96a63d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtYW5nYSUyMGJvb2tzJTIwY29sbGVjdGlvbnxlbnwxfHx8fDE3NzA4ODg4MDF8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
      category: "Manga"
    }
  ];

  const categories = [
    {
      name: "Consolas",
      itemCount: 24,
      image: "https://images.unsplash.com/photo-1644571580646-7048372c491a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxwbGF5c3RhdGlvbiUyMGNvbnRyb2xsZXIlMjBjb25zb2xlfGVufDF8fHx8MTc3MDk3NjMxMnww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
    },
    {
      name: "Videojuegos",
      itemCount: 156,
      image: "https://images.unsplash.com/photo-1548030415-e1eb1c684c9b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBtb3VzZSUyMHBhZCUyMGRlc2t8ZW58MXx8fHwxNzcwOTc2MzE1fDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
    },
    {
      name: "Accesorios Gaming",
      itemCount: 89,
      image: "https://images.unsplash.com/photo-1640823127518-65e1ad563576?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBoZWFkc2V0JTIwZXNwb3J0c3xlbnwxfHx8fDE3NzA5NzYzMTN8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
    },
    {
      name: "Manga & Anime",
      itemCount: 234,
      image: "https://images.unsplash.com/photo-1760113426097-a4076e96a63d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtYW5nYSUyMGJvb2tzJTIwY29sbGVjdGlvbnxlbnwxfHx8fDE3NzA4ODg4MDF8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
    }
  ];

  return (
    <div className="min-h-screen bg-gray-900">
      <Header />
      <Hero />

      {/* Features */}
      <section className="py-12 bg-gray-800 border-b border-gray-700">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div className="flex flex-col items-center text-center">
              <div className="p-4 bg-purple-600/20 rounded-full mb-4 border border-purple-500/30">
                <Truck className="w-8 h-8 text-purple-400" />
              </div>
              <h3 className="font-semibold mb-2 text-white">Envío Gratis</h3>
              <p className="text-sm text-gray-400">En pedidos superiores a €50</p>
            </div>
            <div className="flex flex-col items-center text-center">
              <div className="p-4 bg-purple-600/20 rounded-full mb-4 border border-purple-500/30">
                <Shield className="w-8 h-8 text-purple-400" />
              </div>
              <h3 className="font-semibold mb-2 text-white">Pago Seguro</h3>
              <p className="text-sm text-gray-400">Protección garantizada</p>
            </div>
            <div className="flex flex-col items-center text-center">
              <div className="p-4 bg-purple-600/20 rounded-full mb-4 border border-purple-500/30">
                <Headphones className="w-8 h-8 text-purple-400" />
              </div>
              <h3 className="font-semibold mb-2 text-white">Atención 24/7</h3>
              <p className="text-sm text-gray-400">Siempre disponibles</p>
            </div>
            <div className="flex flex-col items-center text-center">
              <div className="p-4 bg-purple-600/20 rounded-full mb-4 border border-purple-500/30">
                <Star className="w-8 h-8 text-purple-400" />
              </div>
              <h3 className="font-semibold mb-2 text-white">Productos Originales</h3>
              <p className="text-sm text-gray-400">100% auténticos</p>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Products */}
      <section id="productos" className="py-16 bg-gray-900">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4 text-white">Productos Destacados</h2>
            <p className="text-gray-400">Los mejores videojuegos y merchandising anime</p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            {featuredProducts.map((product, index) => (
              <ProductCard key={index} {...product} />
            ))}
          </div>
        </div>
      </section>

      {/* Categories */}
      <section id="categorias" className="py-16 bg-gray-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4 text-white">Explora por Categoría</h2>
            <p className="text-gray-400">Encuentra exactamente lo que buscas</p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {categories.map((category, index) => (
              <CategoryCard key={index} {...category} />
            ))}
          </div>
        </div>
      </section>

      {/* Special Offers */}
      <section id="ofertas" className="py-16 bg-gradient-to-r from-purple-600 to-pink-600">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
          <h2 className="text-4xl font-bold mb-4">Ofertas Especiales</h2>
          <p className="text-xl mb-8 text-white/90">Hasta 40% de descuento en productos seleccionados</p>
          <button className="bg-white text-purple-600 px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transition-colors shadow-lg">
            Ver Todas las Ofertas
          </button>
        </div>
      </section>

      {/* Newsletter */}
      <section className="py-16 bg-gray-900">
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl font-bold mb-4 text-white">Suscríbete a Nuestro Newsletter</h2>
          <p className="text-gray-400 mb-8">Recibe las últimas novedades en gaming y anime</p>
          <div className="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
            <input
              type="email"
              placeholder="Tu correo electrónico"
              className="flex-1 px-4 py-3 rounded-full bg-gray-800 border border-gray-700 text-white focus:outline-none focus:border-purple-500"
            />
            <button className="bg-purple-600 text-white px-8 py-3 rounded-full hover:bg-purple-700 transition-colors shadow-lg shadow-purple-500/30">
              Suscribirse
            </button>
          </div>
        </div>
      </section>

      <Footer />
    </div>
  );
}
