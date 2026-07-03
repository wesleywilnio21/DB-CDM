import React from 'react';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import TextAlign from '@tiptap/extension-text-align';
import { Bold, Italic, Underline as UnderlineIcon, List, ListOrdered, AlignLeft, AlignCenter, AlignRight, AlignJustify } from 'lucide-react';

const MenuBar = ({ editor }) => {
  if (!editor) {
    return null;
  }

  return (
    <div className="border border-gray-200 bg-gray-50 p-2 flex flex-wrap gap-2 rounded-t-md item-center">
      <button
        onClick={() => editor.chain().focus().toggleBold().run()}
        disabled={!editor.can().chain().focus().toggleBold().run()}
        className={`p-2 rounded ${editor.isActive('bold') ? 'bg-gray-200' : 'hover:bg-gray-200'}`}
        type="button"
      >
        <Bold size={16} />
      </button>
      <button
        onClick={() => editor.chain().focus().toggleItalic().run()}
        disabled={!editor.can().chain().focus().toggleItalic().run()}
        className={`p-2 rounded ${editor.isActive('italic') ? 'bg-gray-200' : 'hover:bg-gray-200'}`}
        type="button"
      >
        <Italic size={16} />
      </button>
      <button
        onClick={() => editor.chain().focus().toggleUnderline().run()}
        className={`p-2 rounded ${editor.isActive('underline') ? 'bg-gray-200' : 'hover:bg-gray-200'}`}
        type="button"
      >
        <UnderlineIcon size={16} />
      </button>
      
      <div className="w-px h-6 bg-gray-300 mx-1" />

      <button
        onClick={() => editor.chain().focus().toggleBulletList().run()}
        className={`p-2 rounded ${editor.isActive('bulletList') ? 'bg-gray-200' : 'hover:bg-gray-200'}`}
        type="button"
      >
        <List size={16} />
      </button>
      <button
        onClick={() => editor.chain().focus().toggleOrderedList().run()}
        className={`p-2 rounded ${editor.isActive('orderedList') ? 'bg-gray-200' : 'hover:bg-gray-200'}`}
        type="button"
      >
        <ListOrdered size={16} />
      </button>

      <div className="w-px h-6 bg-gray-300 mx-1" />

      <button
        onClick={() => editor.chain().focus().setTextAlign('left').run()}
        className={`p-2 rounded ${editor.isActive({ textAlign: 'left' }) ? 'bg-gray-200' : 'hover:bg-gray-200'}`}
        type="button"
      >
        <AlignLeft size={16} />
      </button>
      <button
        onClick={() => editor.chain().focus().setTextAlign('center').run()}
        className={`p-2 rounded ${editor.isActive({ textAlign: 'center' }) ? 'bg-gray-200' : 'hover:bg-gray-200'}`}
        type="button"
      >
        <AlignCenter size={16} />
      </button>
      <button
        onClick={() => editor.chain().focus().setTextAlign('right').run()}
        className={`p-2 rounded ${editor.isActive({ textAlign: 'right' }) ? 'bg-gray-200' : 'hover:bg-gray-200'}`}
        type="button"
      >
        <AlignRight size={16} />
      </button>
      <button
        onClick={() => editor.chain().focus().setTextAlign('justify').run()}
        className={`p-2 rounded ${editor.isActive({ textAlign: 'justify' }) ? 'bg-gray-200' : 'hover:bg-gray-200'}`}
        type="button"
      >
        <AlignJustify size={16} />
      </button>
    </div>
  );
};

export default function LetterTemplateEditor({ initialContent = '', onUpdate, variables = [] }) {
  const defaultVariables = [
    { label: 'Nama', key: '{{nama_umat}}' },
    { label: 'Alamat', key: '{{alamat}}' },
    { label: 'Telepon', key: '{{telepon}}' },
    { label: 'NIK', key: '{{nik}}' },
    { label: 'Tanggal Surat', key: '{{tanggal_surat}}' }
  ];

  const sidebarVariables = variables.length ? variables : defaultVariables;

  const editor = useEditor({
    extensions: [
      StarterKit,
      Underline,
      TextAlign.configure({
        types: ['heading', 'paragraph'],
      }),
    ],
    content: initialContent,
    onUpdate: ({ editor }) => {
      onUpdate(editor.getHTML());
    },
    editorProps: {
      attributes: {
        class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto focus:outline-none min-h-[500px] p-4 bg-white',
      },
    },
  });

  const insertVariable = (key) => {
    if (editor) {
      editor.chain().focus().insertContent(key).run();
    }
  };

  return (
    <div className="flex flex-col md:flex-row gap-4">
      {/* Editor Section */}
      <div className="flex-1 flex flex-col border rounded-md shadow-sm">
        <MenuBar editor={editor} />
        <div className="border-t">
          <EditorContent editor={editor} />
        </div>
      </div>

      {/* Sidebar Variables */}
      <div className="w-full md:w-64 shrink-0">
        <div className="bg-white border rounded-md shadow-sm p-4 sticky top-4">
          <h3 className="font-semibold mb-3">Variables (Placeholders)</h3>
          <p className="text-xs text-gray-500 mb-4">Klik variable di bawah untuk menyisipkannya ke lokasi kursor di editor.</p>
          
          <div className="flex flex-col gap-2">
            {sidebarVariables.map((variable) => (
              <button
                key={variable.key}
                type="button"
                className="text-left px-3 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded text-sm transition-colors text-blue-600 font-mono"
                onClick={() => insertVariable(variable.key)}
              >
                {variable.key}
                <span className="block text-xs text-gray-500 font-sans mt-1">{variable.label}</span>
              </button>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}