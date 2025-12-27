import { useState, useRef } from 'react';
import { Upload, X, Image as ImageIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { toast } from 'sonner';

interface ImageUploadProps {
    value?: string | null;
    onChange: (url: string | null) => void;
    accept?: Record<string, string[]>;
    maxSize?: number;
    className?: string;
}

export function ImageUpload({
    value,
    onChange,
    accept = {
        'image/*': ['.png', '.jpg', '.jpeg', '.gif', '.webp'],
    },
    maxSize = 5 * 1024 * 1024, // 5MB
    className = '',
}: ImageUploadProps) {
    const [isUploading, setIsUploading] = useState(false);
    const [preview, setPreview] = useState<string | null>(value || null);
    const [isDragActive, setIsDragActive] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const handleFileSelect = async (file: File) => {
        if (!file) return;

        if (file.size > maxSize) {
            toast.error('File size must be less than 5MB');
            return;
        }

        setIsUploading(true);
        
        try {
            const formData = new FormData();
            formData.append('file', file);

            const response = await fetch('/api/v1/upload/image', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });

            if (!response.ok) {
                throw new Error('Upload failed');
            }

            const result = await response.json();
            const imageUrl = result.data.url;

            setPreview(imageUrl);
            onChange(imageUrl);
            toast.success('Image uploaded successfully');
        } catch (error) {
            toast.error('Failed to upload image');
            console.error('Upload error:', error);
        } finally {
            setIsUploading(false);
        }
    };

    const handleDrop = (e: React.DragEvent) => {
        e.preventDefault();
        setIsDragActive(false);
        
        const files = Array.from(e.dataTransfer.files);
        const imageFile = files.find(file => file.type.startsWith('image/'));
        
        if (imageFile) {
            handleFileSelect(imageFile);
        }
    };

    const handleDragOver = (e: React.DragEvent) => {
        e.preventDefault();
        setIsDragActive(true);
    };

    const handleDragLeave = (e: React.DragEvent) => {
        e.preventDefault();
        setIsDragActive(false);
    };

    const handleFileInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            handleFileSelect(file);
        }
    };

    const handleClick = () => {
        fileInputRef.current?.click();
    };

    const handleRemove = () => {
        setPreview(null);
        onChange(null);
    };

    return (
        <div className={`space-y-2 ${className}`}>
            {preview ? (
                <Card className="relative overflow-hidden">
                    <CardContent className="p-0">
                        <div className="relative group">
                            <img
                                src={preview}
                                alt="Preview"
                                className="w-full h-48 object-cover"
                            />
                            <div className="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    onClick={handleRemove}
                                    className="flex items-center gap-2"
                                >
                                    <X className="h-4 w-4" />
                                    Remove
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            ) : (
                <Card>
                    <CardContent className="p-6">
                        <div
                            onClick={handleClick}
                            onDrop={handleDrop}
                            onDragOver={handleDragOver}
                            onDragLeave={handleDragLeave}
                            className={`border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors ${
                                isDragActive
                                    ? 'border-primary bg-primary/5'
                                    : 'border-muted-foreground/25 hover:border-primary/50'
                            } ${isUploading ? 'pointer-events-none opacity-50' : ''}`}
                        >
                            <input
                                ref={fileInputRef}
                                type="file"
                                accept="image/*"
                                onChange={handleFileInputChange}
                                className="hidden"
                            />
                            <div className="flex flex-col items-center gap-2">
                                {isUploading ? (
                                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                                ) : (
                                    <>
                                        <Upload className="h-8 w-8 text-muted-foreground" />
                                        <div className="text-sm text-muted-foreground">
                                            {isDragActive
                                                ? 'Drop the image here'
                                                : 'Drag & drop an image here, or click to select'}
                                        </div>
                                        <div className="text-xs text-muted-foreground">
                                            PNG, JPG, GIF up to 5MB
                                        </div>
                                    </>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            )}
        </div>
    );
}
