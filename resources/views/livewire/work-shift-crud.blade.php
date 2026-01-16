<div>
    <!-- Main modal -->
    <div id="crud-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-neutral-primary-soft border border-default rounded-base shadow-sm p-4 md:p-6">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        List jam kerja
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="crud-modal">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form wire:submit.prevent="save" class="mb-5">
                    <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                        <div class="col-span-2">
                            <label for="shift" class="block mb-2.5 text-sm font-medium text-heading">Shift</label>
                            <input wire:model="shift" type="text" name="shift" id="shift"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-success focus:border-success block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Shift..." required="">
                        </div>
                        <div class="col-span-2">
                            <label for="late"
                                class="block mb-2.5 text-sm font-medium text-heading">Keterlambatan</label>
                            <input wire:model="late" type="time" name="late" id="late"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-success focus:border-success block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="late...">
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit" data-modal-hide="crud-modal"
                            class="inline-flex items-center  text-white bg-success hover:bg-success-strong box-border border border-transparent focus:ring-4 focus:ring-success-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                            Simpan jam kerja
                        </button>
                    </div>
                </form>

                {{-- Table --}}
                <div
                class="relative overflow-x-auto overflow-y-auto h-50 bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                    <table class="w-full text-sm text-left rtl:text-right text-body">
                        <thead class="bg-neutral-secondary-soft border-b border-default">
                            <tr>
                                <th scope="col" class="px-3 py-1 font-strong text-center">
                                    No
                                </th>
                                <th scope="col" class="px-3 py-1 font-strong text-center">
                                    Shift
                                </th>
                                <th scope="col" class="px-3 py-1 font-strong text-center">
                                    Jam
                                </th>
                                <th scope="col" class="px-3 py-1 font-strong text-center">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shifts as $shift)
                            <tr wire:key="shift-{{ $shift->id }}" class="odd:bg-neutral-primary even:bg-neutral-secondary-soft border-b border-default">
                                <th scope="row" class="px-3 py-2 text-center font-medium text-heading whitespace-nowrap">
                                    {{ $loop->iteration }}
                                </th>
                                <td class="px-3 py-2 text-center">
                                    {{ $shift->shift }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    {{ $shift->late }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button data-modal-hide="crud-modal" type="button" wire:click="delete({{ $shift->id }})" class="font-medium text-danger hover:underline">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Table End --}}

            </div>
        </div>
    </div>

    <!-- Modal show -->
    <button data-modal-target="crud-modal" data-modal-show="crud-modal"
        class="text-white xl:w-60 w-full bg-success box-border border border-transparent hover:bg-success-strong focus:ring-4 focus:ring-success-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none"
        type="button">
        Jam Kerja
    </button>

</div>
