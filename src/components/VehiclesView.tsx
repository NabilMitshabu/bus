import React, { useState } from 'react';
import { Car, Plus, Edit, Trash2, AlertTriangle, CheckCircle, Clock } from 'lucide-react';

const VehiclesView: React.FC = () => {
  const [vehicles] = useState([
    { 
      id: 1, 
      name: 'Bus 001', 
      model: 'Toyota Hiace', 
      capacity: 14, 
      status: 'Actif', 
      driver: 'Jean Mukamba',
      lastMaintenance: '2024-01-15',
      nextMaintenance: '2024-04-15'
    },
    { 
      id: 2, 
      name: 'Bus 002', 
      model: 'Nissan Civilian', 
      capacity: 25, 
      status: 'Maintenance', 
      driver: '-',
      lastMaintenance: '2024-01-10',
      nextMaintenance: '2024-04-10'
    },
    { 
      id: 3, 
      name: 'Bus 003', 
      model: 'Mercedes Sprinter', 
      capacity: 16, 
      status: 'Actif', 
      driver: 'Marie Kabila',
      lastMaintenance: '2024-01-20',
      nextMaintenance: '2024-04-20'
    },
    { 
      id: 4, 
      name: 'Bus 004', 
      model: 'Toyota Quantum', 
      capacity: 12, 
      status: 'Inactif', 
      driver: '-',
      lastMaintenance: '2024-01-05',
      nextMaintenance: '2024-04-05'
    },
  ]);

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'Actif': return <CheckCircle size={16} className="text-green-600" />;
      case 'Maintenance': return <AlertTriangle size={16} className="text-yellow-600" />;
      case 'Inactif': return <Clock size={16} className="text-red-600" />;
      default: return <Clock size={16} className="text-gray-600" />;
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'Actif': return 'bg-green-100 text-green-800';
      case 'Maintenance': return 'bg-yellow-100 text-yellow-800';
      case 'Inactif': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Gestion des Véhicules</h2>
          <p className="text-gray-600">Gérez votre flotte de transport</p>
        </div>
        <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
          <Plus size={20} />
          <span>Nouveau Véhicule</span>
        </button>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-200">
          <div className="flex items-center space-x-3">
            <div className="p-2 bg-blue-100 rounded-lg">
              <Car className="text-blue-600" size={20} />
            </div>
            <div>
              <p className="text-sm text-gray-600">Total</p>
              <p className="text-xl font-bold text-gray-900">{vehicles.length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-200">
          <div className="flex items-center space-x-3">
            <div className="p-2 bg-green-100 rounded-lg">
              <CheckCircle className="text-green-600" size={20} />
            </div>
            <div>
              <p className="text-sm text-gray-600">Actifs</p>
              <p className="text-xl font-bold text-gray-900">
                {vehicles.filter(v => v.status === 'Actif').length}
              </p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-200">
          <div className="flex items-center space-x-3">
            <div className="p-2 bg-yellow-100 rounded-lg">
              <AlertTriangle className="text-yellow-600" size={20} />
            </div>
            <div>
              <p className="text-sm text-gray-600">Maintenance</p>
              <p className="text-xl font-bold text-gray-900">
                {vehicles.filter(v => v.status === 'Maintenance').length}
              </p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-200">
          <div className="flex items-center space-x-3">
            <div className="p-2 bg-red-100 rounded-lg">
              <Clock className="text-red-600" size={20} />
            </div>
            <div>
              <p className="text-sm text-gray-600">Inactifs</p>
              <p className="text-xl font-bold text-gray-900">
                {vehicles.filter(v => v.status === 'Inactif').length}
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* Vehicles Table */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50 border-b border-gray-200">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Véhicule
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Capacité
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Chauffeur
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Prochaine Maintenance
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {vehicles.map((vehicle) => (
                <tr key={vehicle.id} className="hover:bg-gray-50 transition-colors">
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="flex items-center space-x-3">
                      <div className="p-2 bg-blue-100 rounded-lg">
                        <Car className="text-blue-600" size={16} />
                      </div>
                      <div>
                        <p className="text-sm font-medium text-gray-900">{vehicle.name}</p>
                        <p className="text-sm text-gray-500">{vehicle.model}</p>
                      </div>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className="text-sm text-gray-900">{vehicle.capacity} places</span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="flex items-center space-x-2">
                      {getStatusIcon(vehicle.status)}
                      <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(vehicle.status)}`}>
                        {vehicle.status}
                      </span>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className="text-sm text-gray-900">{vehicle.driver}</span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className="text-sm text-gray-900">{vehicle.nextMaintenance}</span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="flex items-center space-x-2">
                      <button className="p-1 text-blue-600 hover:bg-blue-100 rounded transition-colors">
                        <Edit size={16} />
                      </button>
                      <button className="p-1 text-red-600 hover:bg-red-100 rounded transition-colors">
                        <Trash2 size={16} />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default VehiclesView;