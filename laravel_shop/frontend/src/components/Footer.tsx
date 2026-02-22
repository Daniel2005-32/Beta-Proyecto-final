import { ShoppingBag, Mail, Phone, MapPin, Facebook, Instagram, Twitter } from "lucide-react";

export function Footer() {
  return (
    <footer id="contacto" className="bg-gray-950 text-white border-t border-gray-800">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* Brand */}
          <div>
            <div className="flex items-center gap-2 mb-4">
              <ShoppingBag className="w-8 h-8 text-purple-500" />
              <span className="text-xl font-semibold">GameAnime<span className="text-purple-500">Store</span></span>
            </div>
            <p className="text-gray-400 mb-4">
              Tu destino para videojuegos, anime y merchandising de calidad.
            </p>
            <div className="flex gap-4">
              <button className="p-2 bg-gray-800 rounded-full hover:bg-purple-600 transition-colors">
                <Facebook className="w-5 h-5" />
              </button>
              <button className="p-2 bg-gray-800 rounded-full hover:bg-purple-600 transition-colors">
                <Instagram className="w-5 h-5" />
              </button>
              <button className="p-2 bg-gray-800 rounded-full hover:bg-purple-600 transition-colors">
                <Twitter className="w-5 h-5" />
              </button>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="font-semibold mb-4">Enlaces Rápidos</h4>
            <ul className="space-y-2 text-gray-400">
              <li><a href="#" className="hover:text-purple-400 transition-colors">Sobre Nosotros</a></li>
              <li><a href="#" className="hover:text-purple-400 transition-colors">Envíos</a></li>
              <li><a href="#" className="hover:text-purple-400 transition-colors">Devoluciones</a></li>
              <li><a href="#" className="hover:text-purple-400 transition-colors">Términos y Condiciones</a></li>
            </ul>
          </div>

          {/* Customer Service */}
          <div>
            <h4 className="font-semibold mb-4">Atención al Cliente</h4>
            <ul className="space-y-2 text-gray-400">
              <li><a href="#" className="hover:text-purple-400 transition-colors">Preguntas Frecuentes</a></li>
              <li><a href="#" className="hover:text-purple-400 transition-colors">Contacto</a></li>
              <li><a href="#" className="hover:text-purple-400 transition-colors">Garantías</a></li>
              <li><a href="#" className="hover:text-purple-400 transition-colors">Métodos de Pago</a></li>
            </ul>
          </div>

          {/* Contact Info */}
          <div>
            <h4 className="font-semibold mb-4">Contacto</h4>
            <ul className="space-y-3 text-gray-400">
              <li className="flex items-start gap-2">
                <MapPin className="w-5 h-5 mt-0.5 flex-shrink-0" />
                <span>Calle Principal 123, Madrid, España</span>
              </li>
              <li className="flex items-center gap-2">
                <Phone className="w-5 h-5 flex-shrink-0" />
                <span>+34 900 123 456</span>
              </li>
              <li className="flex items-center gap-2">
                <Mail className="w-5 h-5 flex-shrink-0" />
                <span>info@gameanimestore.com</span>
              </li>
            </ul>
          </div>
        </div>

        <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
          <p>&copy; 2026 GameAnimeStore. Todos los derechos reservados.</p>
        </div>
      </div>
    </footer>
  );
}
