<div id="commentModal" class="modal hidden w-full h-full inset-0 flex justify-center items-center transition-opacity duration-500 ease-in-out hover:opacity-95">
    <div class="ModalDialog grow shrink basis-0 p-6 bg-white rounded-2xl shadow flex-col justify-start items-start gap-6 flex transition-transform duration-500 ease-in-out transform hover:scale-105">
        <div class="Title self-stretch justify-between items-start flex w-full">
          <div class="text-dark-sky-600 text-base font-bold capitalize leading-normal">ثبت دیدگاه</div>
          <div class="Icon p-1 rounded justify-center items-center flex">
                <!-- Tabler Icon for Close -->
                <button class="closeModal text-gray-500 hover:text-dark-sky-500 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form id="commentForm" class="w-full">
            <div class="TextInput self-stretch h-20 flex-col justify-start items-start gap-2 flex mt-4">
                <label for="name" class="InputTitle text-right text-dark-sky-600 text-base font-normal capitalize leading-normal">نام</label>
                <input type="text" id="name" name="name"
                    class="InputContainer self-stretch p-3 bg-white rounded-lg border border-gray-300 focus:border-sky-400 focus:ring-2 focus:ring-sky-200 transition-all duration-300"
                    placeholder="نام خود را وارد کنید">
            </div>
            <div class="TextArea self-stretch h-40 flex-col justify-start items-start gap-2 flex mt-4">
                <label for="comment" class="InputTitle text-right text-dark-sky-600 text-base font-normal capitalize leading-normal">متن دیدگاه</label>
                <textarea id="comment" name="comment" rows="4"
                    class="InputContainer self-stretch h-32 px-3 bg-white rounded-lg border border-gray-300 focus:border-sky-400 focus:ring-2 focus:ring-sky-200 transition-all duration-300 resize-none"
                    placeholder="دیدگاه خود را بنویسید"></textarea>
            </div>
            <div class="Button self-stretch mt-6 px-5 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-lg justify-center items-center flex transition-colors duration-300">
                <button type="submit" class="w-full text-center text-white text-base font-medium capitalize leading-normal">
                    ثبت دیدگاه
                </button>
            </div>
        </form>
    </div>
  </div>
  <div id="replyModal" class="modal hidden w-full h-full inset-0 flex justify-center items-center transition-opacity duration-500 ease-in-out hover:opacity-95">
    <div class="ModalDialog grow shrink basis-0 p-6 bg-white rounded-2xl shadow flex-col justify-start items-start gap-6 flex transition-transform duration-500 ease-in-out transform hover:scale-105">
        <div class="Title self-stretch justify-between items-start flex w-full">
          <div class="text-dark-sky-600 text-base font-bold capitalize leading-normal">پاسخ به <strong class="text-yellow-500" id="replyToName"></strong></div>
          <div class="Icon p-1 rounded justify-center items-center flex">
                <!-- Tabler Icon for Close -->
                <button class="closeModal text-gray-500 hover:text-dark-sky-500 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form id="replyForm" class="w-full">
            <input type="hidden" id="replyTo" name="replyTo">
            <div class="TextArea self-stretch h-40 flex-col justify-start items-start gap-2 flex mt-4">
                <label for="reply" class="InputTitle text-right text-dark-sky-600 text-base font-normal capitalize leading-normal">پاسخ</label>
                <textarea id="reply" name="reply" rows="4"
                    class="InputContainer self-stretch h-32 px-3 bg-white rounded-lg border border-gray-300 focus:border-sky-400 focus:ring-2 focus:ring-sky-200 transition-all duration-300 resize-none"
                    placeholder="پاسخ خود را بنویسید"></textarea>
            </div>
            <div class="Button self-stretch mt-6 px-5 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-lg justify-center items-center flex transition-colors duration-300">
                <button type="submit" class="w-full text-center text-white text-base font-medium capitalize leading-normal">
                    ارسال پاسخ
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
  @vite(['resources/js/comment.js'])
@endpush
