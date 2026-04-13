<div class="row g-4">
    {{-- Kiri: Nama, Warna, Catatan --}}
    <div class="col-md-8">
        <x-form.text
            name="name"
            label="Nama Status Label"
            :value="$statusLabel->name ?? null"
            help="Nama label status harus unik."
            required
        />

        {{-- Color Palette --}}
        @php
            $selectedColor = $statusLabel->color ?? '#6c757d';
            $palette = [
                '#ef4444', '#f97316', '#f59e0b', '#eab308',
                '#84cc16', '#22c55e', '#10b981', '#14b8a6',
                '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6',
                '#a855f7', '#ec4899', '#f43f5e', '#6b7280',
                '#94a3b8', '#1e293b', '#000000', '#ffffff',
            ];
        @endphp
        <div class="mb-3">
            <label class="form-label">Warna</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($palette as $hex)
                    <label
                        class="color-swatch"
                        title="{{ $hex }}"
                        style="
                            width: 28px; height: 28px;
                            background-color: {{ $hex }};
                            border-radius: 6px;
                            cursor: pointer;
                            border: 3px solid {{ $selectedColor === $hex ? '#0d6efd' : 'transparent' }};
                            outline: 2px solid rgba(0,0,0,0.15);
                            transition: border-color .15s;
                        "
                    >
                        <input
                            type="radio"
                            name="color"
                            value="{{ $hex }}"
                            class="visually-hidden color-radio"
                            {{ $selectedColor === $hex ? 'checked' : '' }}
                        >
                    </label>
                @endforeach
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.color-radio').forEach(function (radio) {
                    radio.addEventListener('change', function () {
                        document.querySelectorAll('.color-swatch').forEach(function (swatch) {
                            swatch.style.borderColor = 'transparent';
                        });
                        radio.closest('.color-swatch').style.borderColor = '#0d6efd';
                    });
                });
            });
        </script>

        <x-form.textarea
            name="notes"
            label="Catatan"
            :value="$statusLabel->notes ?? null"
            rows="5"
            placeholder="Tambahkan catatan internal bila diperlukan"
        />
    </div>

    {{-- Kanan: Toggle options --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Opsi Status</label>
        <div class="d-flex flex-column gap-3 mt-1">
            <x-form.switch
                name="deployable"
                label="Deployable"
                :checked="$statusLabel->deployable ?? false"
                help="Status ini dapat digunakan untuk aset yang siap dipakai."
            />
            <x-form.switch
                name="pending"
                label="Pending"
                :checked="$statusLabel->pending ?? false"
                help="Status ini menandai aset yang masih menunggu proses."
            />
            <x-form.switch
                name="archived"
                label="Archived"
                :checked="$statusLabel->archived ?? false"
                help="Status ini menandai aset yang sudah diarsipkan."
            />
            <x-form.switch
                name="show_in_nav"
                label="Show in Navigation"
                :checked="$statusLabel->show_in_nav ?? false"
                help="Tampilkan status ini pada navigasi atau filter utama."
            />
            <x-form.switch
                name="default_label"
                label="Default Label"
                :checked="$statusLabel->default_label ?? false"
                help="Gunakan label ini sebagai pilihan default."
            />
        </div>
    </div>
</div>
