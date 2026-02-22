import { ShoppingCart, Heart } from "lucide-react";
import { ImageWithFallback } from "./figma/ImageWithFallback";

interface ProductCardProps {
  name: string;
  price: number;
  originalPrice?: number;
  image: string;
  category: string;
}

export function ProductCard({ name, price, originalPrice, image, category }: ProductCardProps) {
  return (
    <div className="group bg-gray-800 rounded-lg overflow-hidden shadow-md hover:shadow-xl hover:shadow-purple-500/20 transition-all border border-gray-700">
      <div className="relative overflow-hidden aspect-square">
        <ImageWithFallback
          src={image}
          alt={name}
          className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
        />
        <button className="absolute top-4 right-4 p-2 bg-gray-900/90 rounded-full hover:bg-purple-600 transition-colors">
          <Heart className="w-5 h-5 text-white" />
        </button>
        {originalPrice && (
          <div className="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
            -{Math.round(((originalPrice - price) / originalPrice) * 100)}%
          </div>
        )}
      </div>
      <div className="p-4">
        <p className="text-sm text-purple-400 mb-1">{category}</p>
        <h3 className="font-semibold text-white mb-2">{name}</h3>
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2">
            <span className="text-xl font-bold text-purple-400">€{price}</span>
            {originalPrice && (
              <span className="text-sm text-gray-500 line-through">€{originalPrice}</span>
            )}
          </div>
          <button className="p-2 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition-colors">
            <ShoppingCart className="w-5 h-5" />
          </button>
        </div>
      </div>
    </div>
  );
}
