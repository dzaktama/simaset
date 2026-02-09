<header id="page-topbar" class="fixed top-0 right-0 left-0 z-30 bg-white border-b border-gray-200 transition-all duration-300 h-14" :class="sidebarOpen ? 'md:left-64' : 'md:left-0'">
    <div class="flex items-center justify-between h-full px-1 gap-0">
        
        {{-- Left: Hamburger & Mobile Title --}}
        <div class="flex items-center gap-2 shrink-0 px-2 h-full">
            {{-- Hamburger Menu (Mobile) --}}
            <button onclick="toggleMobileSidebar()" class="md:hidden text-gray-500 hover:text-indigo-600 transition-colors p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            
            {{-- Desktop Sidebar Toggle (Always Visible on Desktop) --}}
            <button @click="sidebarOpen = !sidebarOpen" class="hidden md:flex text-gray-500 hover:text-indigo-600 transition-colors p-1.5 rounded hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>

        {{-- Middle: Chrome-like Tabs (Scrollable & Full Height) --}}
        <div class="flex-1 overflow-hidden h-full flex items-end" x-data="tabSystem()" x-init="initTabs()" @tab-update.window="handleTabUpdate($event.detail)">
            <div class="flex items-end w-full overflow-x-auto scrollbar-hide scroll-smooth h-full pt-2 px-1 gap-1">
                
                <template x-for="(tab, index) in tabs" :key="tab.url">
                    <div 
                        draggable="true"
                        @dragstart="dragStart($event, index)"
                        @dragenter="dragEnter($event, index)"
                        @dragend="dragEnd()"
                        class="group relative flex items-center gap-1.5 px-3 min-w-[150px] max-w-[200px] shrink-0 rounded-t-lg transition-all duration-100 cursor-pointer select-none border-t border-r border-l h-full mt-auto pt-2.5"
                        :class="{
                            'bg-white text-indigo-700 border-gray-300 shadow-[0_-2px_5px_rgba(0,0,0,0.02)] z-10 font-bold': currentUrl === tab.url,
                            'bg-gray-50 text-gray-500 border-gray-200 hover:bg-gray-100 hover:text-gray-700': currentUrl !== tab.url,
                            'opacity-50 scale-95': draggingIndex === index
                        }"
                        @click="navigateTab(tab.url)"
                    >
                        {{-- Drag Handle / Number --}}
                        <span class="absolute top-0.5 right-1.5 text-[8px] font-mono text-gray-400 opacity-60 pointer-events-none" x-text="index + 1"></span>

                        {{-- Icon (Dynamic HTML) --}}
                        <div class="shrink-0 w-3.5 h-3.5 flex items-center justify-center" :class="currentUrl === tab.url ? 'text-indigo-600' : 'text-gray-400'" x-html="tab.icon"></div>
                        
                        <span class="truncate text-xs leading-tight block w-full pr-3" x-text="tab.title"></span>

                        {{-- Close Btn --}}
                        <button @click.stop="closeTab(index)" class="ml-auto p-0.5 rounded text-gray-400 opacity-0 group-hover:opacity-100 hover:bg-red-50 hover:text-red-500 transition-all shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        
                        {{-- Active Indicator Line (Top) --}}
                        <div x-show="currentUrl === tab.url" class="absolute top-[-1px] left-0 right-0 h-[2px] bg-indigo-600 rounded-t-lg z-20"></div>
                        
                        {{-- Mask Bottom Border to blend with content --}}
                        <div x-show="currentUrl === tab.url" class="absolute bottom-[-1px] left-0 right-0 h-[3px] bg-white z-20"></div>
                    </div>
                </template>

            </div>
        </div>

        {{-- Right Side: Date/Time & Actions --}}
        <div class="flex items-center gap-3 shrink-0 border-l border-gray-200 pl-3 pr-4 h-3/4 my-auto">
                {{-- Date & Time (Small) --}}
            <div class="hidden md:flex flex-col items-end mr-1">
                <span class="text-[10px] font-bold text-gray-700" id="header-date">{{ now()->isoFormat('ddd, D MMM') }}</span>
                <span class="text-[10px] font-mono text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded" id="header-clock">{{ now()->format('H:i') }}</span>
            </div>

                {{-- Notification Bell --}}
            <button class="relative p-1.5 text-gray-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-gray-100">
                <span class="absolute top-1.5 right-1.5 h-1.5 w-1.5 rounded-full bg-red-500 border-2 border-white"></span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </button>
        </div>
    </div>
</header>

<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>

<script>
    // Update Clock Script
     setInterval(() => {
        const now = new Date();
        const options = { weekday: 'short', day: 'numeric', month: 'short' };
        document.getElementById('header-date').innerText = now.toLocaleDateString('id-ID', options);
        document.getElementById('header-clock').innerText = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit', second:'2-digit'});
    }, 1000);

    // Global function for PJAX sync
    window.updateTabSystemWithUrl = function(url, title) {
        // Dispatch Custom Event for Alpine to catch
        window.dispatchEvent(new CustomEvent('tab-update', { detail: { url: url, title: title } }));
    };

    function tabSystem() {
        // Helper to find icon from sidebar
        const findIcon = (url) => {
            // Normalize URL (remove query params)
            const cleanUrl = url.split('?')[0];
            // Find link in sidebar
            const link = document.querySelector(`aside a[href^="${cleanUrl}"]`);
            if (link) {
                const svg = link.querySelector('svg');
                if (svg) return svg.outerHTML;
            }
            // Default Icon
            return '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>';
        };

        const storageKey = 'simaset_tabs_' + (window.currentUserId || 'guest');

        return {
            tabs: JSON.parse(localStorage.getItem(storageKey)) || [],
            currentUrl: window.location.href,
            draggingIndex: null, // Track dragged item
            
            initTabs() {
                this.$nextTick(() => {
                    const title = document.title || 'Page';
                    
                    // Self-heal: Update ALL icons to ensure consistency and fix 'undefined'
                    this.tabs.forEach(t => {
                        t.icon = findIcon(t.url) || findIcon(''); // Fallback to default
                    });

                    if (!this.tabs.find(t => t.url === this.currentUrl)) {
                        this.addTab(title, this.currentUrl);
                    } else {
                        // Refresh title for current tab
                         const tab = this.tabs.find(t => t.url === this.currentUrl);
                         if(tab) tab.title = title;
                         this.saveTabs();
                    }
                });
            },

            handleTabUpdate(detail) {
                if(this.currentUrl === detail.url) return;
                this.currentUrl = detail.url;
                if (!this.tabs.find(t => t.url === detail.url)) {
                    this.addTab(detail.title, detail.url);
                }
            },

            addTab(title, url) {
                if (this.tabs.some(t => t.url === url)) return;
                
                // NO LIMIT: Removed tab limit check
                // if (this.tabs.length >= 8) this.tabs.shift();

                this.tabs.push({ 
                    title: title, 
                    url: url,
                    icon: findIcon(url) 
                });
                this.saveTabs();
            },

            closeTab(index) {
                const tabToRemove = this.tabs[index];
                this.tabs.splice(index, 1);
                this.saveTabs();
                
                // If closed active tab, navigate to last tab or dashboard
                if (tabToRemove.url === this.currentUrl) {
                    if (this.tabs.length > 0) {
                        this.navigateTab(this.tabs[this.tabs.length - 1].url);
                    } else {
                        this.navigateTab('{{ route('dashboard') }}');
                    }
                }
            },

            navigateTab(url) {
                this.currentUrl = url;
                if(typeof navigate === 'function') {
                    navigate(url);
                } else {
                    window.location.href = url;
                }
            },

            dragStart(event, index) {
                this.draggingIndex = index;
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.dropEffect = 'move';
                event.dataTransfer.setData('text/plain', index);
            },

            dragEnter(event, targetIndex) {
                if (this.draggingIndex !== null && this.draggingIndex !== targetIndex) {
                    // Remove from old position
                    const item = this.tabs.splice(this.draggingIndex, 1)[0];
                    // Insert at new position
                    this.tabs.splice(targetIndex, 0, item);
                    // Update index to follow the item
                    this.draggingIndex = targetIndex;
                    this.saveTabs();
                }
            },

            dragEnd() {
                this.draggingIndex = null;
                this.saveTabs();
            },

            saveTabs() {
                // NO LIMIT: Removed tab limit shift
                // if(this.tabs.length > 8) this.tabs.shift();
                try {
                    localStorage.setItem(storageKey, JSON.stringify(this.tabs));
                } catch(e) { console.error('Tab save failed', e); }
            }
        }
    }

    // Sidebar Toggle Logic (Proxied)
    function handleSidebarToggle() {
        if (typeof toggleMobileSidebar === 'function') {
            toggleMobileSidebar();
        }
    }
</script>