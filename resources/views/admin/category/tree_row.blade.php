<tr>
    <td class="sr_no">
        {{-- Generate the Sr. No --}}
        {{ ($categories->currentpage() - 1) * $categories->perpage() + $loop->parent->index + 1 }}
    </td>
    <td>{{ $category->id }}</td>
    <td class="img_name_gap">
        <img width="50px" height="50px" src="{{ asset('admin-assets/assets/img/category/' . $category->image) }}" 
             alt="category picture" style="border-radius:10px;">
    </td>
    <td>
        {{-- Indent child categories --}}
        {!! str_repeat('--', $level) !!} 
        {{ $category->category_name }}
    </td>
    <td>
        {{ $category->parent ? $category->parent->category_name : '' }}
    </td>
    <td>{{ $category->category_order }}</td>
    <td>
        @if ($category->status == 1)
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-danger">Inactive</span>
        @endif
    </td>
    <td>
        <div class="d-flex">
            <a href="" class="edit_category text-info"
                category_id="{{ $category->id }}"
                category_name="{{ $category->category_name }}"
                parent_category="{{ $category->parent_id }}"
                category_icon="{{ $category->image }}"
                category_icon_path="{{ asset('admin-assets/assets/img/category') }}"
                category_banner="{{ $category->banner_image }}"
                category_banner_path="{{ asset('admin-assets/assets/img/category_banner_image') }}"
                category_status="{{ $category->status }}"
                category_order="{{ $category->category_order }}">
                <i class="fa fa-pencil" style="margin-right:10px; font-size:18px;"></i>
            </a>
            <form method="post" action="{{ route('category.destroy', $category->id) }}">
                @csrf
                @method('DELETE')
                <a href="{{ route('category.destroy', $category->id) }}" 
                   class="delete_category text-danger show_confirm">
                    <i class="fa fa-trash" style="margin-right:10px; font-size:18px;"></i>
                </a>
            </form>
        </div>
    </td>
</tr>

{{-- Recursively render child categories --}}
@if ($category->childrenRecursive->isNotEmpty())
    @foreach ($category->childrenRecursive as $child)
        @include('admin.category.tree_row', ['category' => $child, 'level' => $level + 1])
    @endforeach
@endif