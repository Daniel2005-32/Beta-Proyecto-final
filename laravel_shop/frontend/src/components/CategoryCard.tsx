import { ImageWithFallback } from "./figma/ImageWithFallback";

interface CategoryCardProps {
  name: string;
  itemCount: number;
  image: string;
}

export function CategoryCard({ name, itemCount, image }: CategoryCardProps) {
  return (
    <div className="group relative overflow-hidden rounded-lg aspect-square cursor-pointer">
      <ImageWithFallback
        src={image}
        alt={name}
        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
      />
      <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-black/20"></div>
      <div className="absolute bottom-0 left-0 right-0 p-6 text-white">
        <h3 className="text-2xl font-bold mb-1">{name}</h3>
        <p className="text-white/80">{itemCount} productos</p>
      </div>
    </div>
  );
}
