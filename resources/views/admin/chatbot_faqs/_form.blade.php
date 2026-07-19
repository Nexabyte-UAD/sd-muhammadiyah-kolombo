{{--
    Form Partial FAQ Chatbot (admin/chatbot_faqs/_form.blade.php)
    Digunakan bersama oleh halaman tambah (create) dan sunting (edit) FAQ chatbot.
--}}
<div class="form-grid">
    <div class="form-field form-field-full">
        <label for="question" class="form-label">Pertanyaan <span>*</span></label>
        <input type="text" name="question" id="question"
               class="form-control-admin @error('question') is-invalid @enderror"
               value="{{ old('question', optional($chatbotFaq ?? null)->question) }}"
               placeholder="Contoh: Di mana alamat sekolah?" required maxlength="255">
        @error('question')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field form-field-full">
        <label for="answer" class="form-label">Jawaban <span>*</span></label>
        <textarea name="answer" id="answer" rows="8"
                  class="form-control-admin @error('answer') is-invalid @enderror"
                  placeholder="Tulis jawaban yang jelas dan akurat..." required maxlength="3000">{{ old('answer', optional($chatbotFaq ?? null)->answer) }}</textarea>
        @error('answer')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field form-field-full">
        <label for="keywords" class="form-label">Kata Kunci</label>
        <input type="text" name="keywords" id="keywords"
               class="form-control-admin @error('keywords') is-invalid @enderror"
               value="{{ old('keywords', optional($chatbotFaq ?? null)->keywords) }}"
               placeholder="Contoh: alamat, lokasi, letak sekolah"
               maxlength="1000">
        <div class="form-help">Pisahkan setiap kata kunci menggunakan koma. Contoh: <em>alamat, lokasi, letak sekolah</em></div>
        @error('keywords')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field">
        <label for="category" class="form-label">Kategori</label>
        <input type="text" name="category" id="category"
               class="form-control-admin @error('category') is-invalid @enderror"
               value="{{ old('category', optional($chatbotFaq ?? null)->category) }}"
               placeholder="Contoh: Kontak, Akademik, Fasilitas"
               maxlength="100">
        <div class="form-help">Kategori membantu mengelompokkan FAQ agar lebih mudah difilter.</div>
        @error('category')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field">
        <label for="is_active" class="form-label">Status Aktif</label>
        <div style="display: flex; align-items: center; gap: 10px; margin-top: 6px;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                   class="@error('is_active') is-invalid @enderror"
                   style="width: 18px; height: 18px; accent-color: #10b981; cursor: pointer;"
                   {{ old('is_active', optional($chatbotFaq ?? null)->is_active ?? true) ? 'checked' : '' }}>
            <label for="is_active" style="margin: 0; cursor: pointer;">
                FAQ ini aktif dan akan digunakan oleh chatbot
            </label>
        </div>
        @error('is_active')<div class="form-error">{{ $message }}</div>@enderror
    </div>
</div>
