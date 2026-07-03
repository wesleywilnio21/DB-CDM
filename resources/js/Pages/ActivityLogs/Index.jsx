import React, { useState } from 'react';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";

export default function ActivityLogDashboard({ logs }) {
  const [selectedLog, setSelectedLog] = useState(null);

  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold mb-4">Activity Log Dashboard</h1>
      
      <div className="border rounded-md">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Time</TableHead>
              <TableHead>Pelaku (User)</TableHead>
              <TableHead>Deskripsi Aktivitas</TableHead>
              <TableHead>Nama Model</TableHead>
              <TableHead>Aksi</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {logs.data.map((log) => (
              <TableRow key={log.id}>
                <TableCell>{new Date(log.created_at).toLocaleString()}</TableCell>
                <TableCell>{log.causer?.name || 'System'}</TableCell>
                <TableCell>{log.description}</TableCell>
                <TableCell>{log.subject_type?.split('\\').pop()}</TableCell>
                <TableCell>
                  <Dialog>
                    <DialogTrigger asChild>
                      <Button variant="outline" size="sm" onClick={() => setSelectedLog(log)}>
                        Lihat Detail (JSON)
                      </Button>
                    </DialogTrigger>
                    <DialogContent className="max-w-xl">
                      <DialogHeader>
                        <DialogTitle>Detail Perubahan (Lama vs Baru)</DialogTitle>
                      </DialogHeader>
                      <div className="p-4 bg-gray-900 text-green-400 rounded-md overflow-auto max-h-[60vh]">
                        <pre>
                          {JSON.stringify(log.properties, null, 2)}
                        </pre>
                      </div>
                    </DialogContent>
                  </Dialog>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>

      {/* Basic Pagination - Adjust depending on Inertia/React setup */}
      <div className="mt-4 flex justify-between">
        <Button disabled={!logs.prev_page_url}>Previous</Button>
        <Button disabled={!logs.next_page_url}>Next</Button>
      </div>
    </div>
  );
}