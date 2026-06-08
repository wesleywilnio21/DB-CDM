import { createClient } from "@supabase/supabase-js";

const supabaseUrl = https://jmzbgxiqijhkfpxjxruf.supabase.co
const supabaseAnonKey = eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImptemJneGlxaWpoa2ZweGp4cnVmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODA0ODIyOTksImV4cCI6MjA5NjA1ODI5OX0.ZP0CT2y_B6bZzQGLzMnpuMZLea7qsBuXYSiCdeTdG2k
export const supabase = createClient(supabaseUrl, supabaseAnonKey);
