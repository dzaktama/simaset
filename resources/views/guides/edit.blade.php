@extends('layouts.main')

@section('container')
<div class="min-h-screen bg-gray-100 p-6" x-data="guideEditor({{ json_encode($guide ?? ['steps' => []]) }})">
    
    {{-- Top Bar --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            {{ $guide ? 'Edit Panduan' : 'Buat Panduan Baru' }}
        </h1>
        <div class="flex gap-3">
             <a href="{{ route('guides.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</a>
             <button type="submit" form="guideForm" class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-lg shadow hover:bg-indigo-700">
                Simpan Panduan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 h-[calc(100vh-8rem)]">
        
        {{-- LEFT COLUMN: EDITOR FORM --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 flex flex-col overflow-hidden h-full">
            <div class="p-4 bg-gray-50 border-b border-gray-200 font-bold text-gray-700 flex justify-between items-center">
                <span>Editor Konten</span>
                <span class="text-xs text-gray-500 font-normal">Perubahan tersimpan otomatis di preview</span>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6">
                <form id="guideForm" action="{{ $guide ? route('guides.update', $guide->id) : route('guides.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @if($guide) @method('PUT') @endif

                    {{-- Guide Metadata --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID (Kode Unik)</label>
                            <input type="text" name="id" x-model="form.id" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 font-medium" {{ $guide ? 'readonly' : '' }} placeholder="contoh: user-guide">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Judul Panduan</label>
                            <input type="text" name="title" x-model="form.title" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-lg text-gray-900 font-bold focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 placeholder-gray-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deskripsi Singkat</label>
                            <textarea name="description" x-model="form.description" rows="2" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 font-medium"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Warna Tema</label>
                                <select name="color" x-model="form.color" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 font-medium cursor-pointer">
                                    <option value="blue">Blue</option>
                                    <option value="indigo">Indigo</option>
                                    <option value="green">Green</option>
                                    <option value="red">Red</option>
                                    <option value="amber">Amber</option>
                                    <option value="teal">Teal</option>
                                    <option value="purple">Purple</option>
                                    <option value="gray">Gray</option>
                                </select>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Icon (Heroicon Name)</label>
                                <select name="icon" x-model="form.icon" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 font-medium cursor-pointer">
                                    <option value="book-open">Book Open</option>
                                    <option value="question-mark-circle">Question Mark</option>
                                    <option value="hand-raised">Hand Raised</option>
                                    <option value="exclamation-triangle">Warning</option>
                                    <option value="cube">Cube (Asset)</option>
                                    <option value="clipboard-check">Clipboard Check</option>
                                    <option value="users">Users</option>
                                    <option value="cog">Cog (Settings)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    {{-- Steps Repeater --}}
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Langkah-Langkah</h3>
                            <button type="button" @click="addStep()" class="text-sm text-indigo-600 font-bold hover:underline">+ Tambah Langkah</button>
                        </div>

                        <div class="space-y-6">
                            <template x-for="(step, index) in form.steps" :key="index">
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 relative group">
                                    {{-- Delete Step Button --}}
                                    <button type="button" @click="removeStep(index)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>

                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="bg-gray-200 text-gray-600 font-bold text-xs px-2 py-1 rounded">Step <span x-text="index + 1"></span></span>
                                    </div>

                                    <input type="hidden" :name="'steps['+index+'][id]'" :value="step.id">
                                    
                                    <div class="space-y-3">
                                        <input type="text" :name="'steps['+index+'][title]'" x-model="step.title" placeholder="Judul Langkah" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 font-bold focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 placeholder-gray-400">
                                        
                                        <textarea :name="'steps['+index+'][description]'" x-model="step.description" rows="3" placeholder="Penjelasan detail..." class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 font-medium placeholder-gray-400"></textarea>
                                        
                                        {{-- Image Upload --}}
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-2">Visualisasi Langkah (16:9)</label>
                                            <div class="flex flex-col sm:flex-row gap-4 items-start">
                                                <div class="flex-shrink-0 w-full sm:w-48 aspect-video bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden hover:bg-gray-50 transition relative">
                                                    <template x-if="step.image_preview || step.image">
                                                        <img :src="step.image_preview || (step.image ? '/storage/'+step.image : null)" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!step.image_preview && !step.image">
                                                        <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                    </template>
                                                    
                                                    {{-- File Input Overlay --}}
                                                    <input type="file" :name="'steps['+index+'][image_file]'" accept="image/*" @change="handleImageUpload($event, index)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                                </div>
                                                
                                                <div class="flex-1 text-xs text-gray-500">
                                                     <p class="mb-2">Klik area gambar untuk mengunggah atau mengganti foto. Pastikan resolusi landscape (16:9) untuk hasil terbaik.</p>
                                                     <input type="hidden" :name="'steps['+index+'][image_path]'" :value="step.image"> 
                                                     <button type="button" x-show="step.image_preview || step.image" @click="step.image_preview = null; step.image = null" class="text-red-500 font-bold hover:underline">Hapus Gambar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <button type="button" @click="addStep()" class="mt-4 w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 font-bold hover:border-indigo-500 hover:text-indigo-600 transition-colors">
                            + Tambah Langkah Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- RIGHT COLUMN: LIVE PREVIEW --}}
        <div class="hidden lg:flex flex-col h-full">
            <div class="p-2 bg-indigo-900 text-white text-center text-sm font-bold rounded-t-xl">
                Live Preview
            </div>
            <div class="flex-1 bg-white border-x border-b border-gray-200 shadow-2xl overflow-y-auto relative rounded-b-xl">
                
                {{-- PREVIEW CONTENT (Mirrors show.blade.php logic) --}}
                <div class="w-full min-h-full bg-white pb-20">
                    {{-- Header Preview --}}
                    <div class="relative py-10 px-8 border-b" 
                         :class="{
                            'bg-blue-50 border-blue-200': form.color == 'blue',
                            'bg-indigo-50 border-indigo-200': form.color == 'indigo',
                            'bg-green-50 border-green-200': form.color == 'green',
                            'bg-red-50 border-red-200': form.color == 'red',
                            'bg-amber-50 border-amber-200': form.color == 'amber',
                            'bg-teal-50 border-teal-200': form.color == 'teal',
                            'bg-purple-50 border-purple-200': form.color == 'purple',
                            'bg-gray-50 border-gray-200': form.color == 'gray',
                         }">
                        <div class="flex flex-col gap-4">
                            <span class="text-xs font-bold uppercase tracking-wider opacity-50">Header Preview</span>
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-white rounded-xl shadow-sm">
                                    {{-- Simplified Icon Preview --}}
                                    <svg x-show="form.icon == 'book-open'" class="w-8 h-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                    <svg x-show="form.icon != 'book-open'" class="w-8 h-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <h1 class="text-2xl font-extrabold text-gray-900" x-text="form.title || 'Judul Panduan'"></h1>
                            </div>
                            <p class="text-gray-600" x-text="form.description || 'Deskripsi panduan akan muncul di sini...'"></p>
                        </div>
                    </div>

                    {{-- Steps Preview --}}
                    <div class="p-8 space-y-8">
                        <template x-for="(step, index) in form.steps" :key="index">
                            <div class="flex gap-4">
                                {{-- Circle --}}
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
                                         :class="{
                                            'bg-blue-600': form.color == 'blue',
                                            'bg-indigo-600': form.color == 'indigo',
                                            'bg-green-600': form.color == 'green',
                                            'bg-red-600': form.color == 'red',
                                            'bg-amber-600': form.color == 'amber',
                                            'bg-teal-600': form.color == 'teal',
                                            'bg-purple-600': form.color == 'purple',
                                            'bg-gray-600': form.color == 'gray',
                                         }"
                                         x-text="index + 1"></div>
                                </div>
                                
                                {{-- Content --}}
                                <div class="flex-1 bg-white border border-gray-100 shadow-sm rounded-xl p-6">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2" x-text="step.title || 'Judul Langkah'"></h3>
                                    <p class="text-gray-600 text-sm mb-4 whitespace-pre-line" x-text="step.description || 'Penjelasan...'"></p>
                                    
                                    {{-- Image Preview --}}
                                    <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                                         <template x-if="step.image_preview || step.image">
                                            <img :src="step.image_preview || '/storage/'+step.image" class="w-full h-auto object-cover">
                                        </template>
                                        <template x-if="!step.image_preview && !step.image">
                                            <div class="h-32 flex items-center justify-center text-gray-300 text-xs">No Image</div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="form.steps.length === 0" class="text-center text-gray-400 py-10">
                            Belum ada langkah yang ditambahkan.
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
    function guideEditor(initialData) {
        return {
            form: {
                id: initialData.id || '',
                title: initialData.title || '',
                description: initialData.description || '',
                color: initialData.color || 'blue',
                icon: initialData.icon || 'book-open',
                steps: initialData.steps ? initialData.steps.map(s => ({...s, image_preview: null})) : []
            },
            
            addStep() {
                this.form.steps.push({
                    id: null,
                    title: '',
                    description: '',
                    image: null,
                    image_preview: null
                });
            },

            removeStep(index) {
                if(confirm('Hapus langkah ini?')) {
                    this.form.steps.splice(index, 1);
                }
            },

            handleImageUpload(event, index) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.form.steps[index].image_preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        }
    }
</script>
@endsection
